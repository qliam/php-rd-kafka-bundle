<?php
/**
 * Created by PhpStorm.
 * User: Victor
 * Date: 13/01/2017
 * Time: 09:43
 */

namespace Mshauneu\RdKafkaBundle\Zookeeper;


use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;

class ZookeeperApi
{

    /**
     * @var string
     */
    protected $hosts;

    /**
     * @var \Zookeeper
     */
    protected $zk;

    /**
     * @var bool
     */
    protected $isConnected;


    /**
     * ZookeeperApi constructor.
     */
    function __construct()
    {
        $this->zk = new \Zookeeper();
        $this->isConnected = false;
    }

    /**
     * @param string $hosts
     */
    public function setHosts($hosts)
    {
        $this->hosts = $hosts;
    }

    private function connectIfNeeded()
    {
        if ($this->isConnected === false) {
            $this->zk->connect($this->hosts);
            $this->isConnected = true;
        }
    }

    /**
     * @param string $topic
     * @return array
     */
    public function resolveTopic(string $topic)
    {
        $this->connectIfNeeded();
        try {
            $topicInfos = $this->zk->get('/brokers/topics/' . $topic);
            return json_decode($topicInfos, true);
        } catch (\Exception $e) {
            return array();
        }
    }

    /**
     * @param string $brokerId
     * @return array|mixed
     */
    private function getBrokerInfos(string $brokerId)
    {
        try {
            $brokersInfos = $this->zk->get('/brokers/ids/' . $brokerId);
            return json_decode($brokersInfos, true);
        } catch (\Exception $e) {
            return array();
        }
    }

    /**
     * @param string $topicName
     * @param array $topicInfos
     * @return array
     */
    public function resolveBrokers(string $topicName, array $topicInfos)
    {
        $partitionsSummary = array();
        foreach ($topicInfos['partitions'] as $partition => $brokersId) {
            $partitionsSummary[$partition]['brokers'] = array();
            foreach ($brokersId as $brokerId) {
                $brokerInfos = $this->getBrokerInfos($brokerId);
                $partitionsSummary[$partition]['brokers'][] = $brokerInfos['host'] . ':' . $brokerInfos['port'];
            }
        }
        $brokersSummary = array($topicName => $partitionsSummary, 'version' => $topicInfos['version']);
        return $brokersSummary;
    }

    public function resolve(string $topic)
    {
        $topicInfos = $this->resolveTopic($topic);
        $brokersSummary = $this->resolveBrokers($topic, $topicInfos);
        return $brokersSummary;

    }
}