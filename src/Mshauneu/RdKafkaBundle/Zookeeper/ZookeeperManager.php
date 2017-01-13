<?php
/**
 * Created by PhpStorm.
 * User: Victor
 * Date: 12/01/2017
 * Time: 17:14
 */

namespace Mshauneu\RdKafkaBundle\Zookeeper;


class ZookeeperManager
{

    /**
     * @var ZookeeperApi
     */
    protected $zookeeperApi;

    /**
     * @var ZookeeperCache
     */
    protected $zookeeperCache;

    /**
     * ZookeeperManager constructor.
     * @param ZookeeperApi $zookeeperApi
     * @param ZookeeperCache $zookeeperCache
     */
    function __construct(ZookeeperApi $zookeeperApi, ZookeeperCache $zookeeperCache)
    {
        $this->zookeeperApi = $zookeeperApi;
        $this->zookeeperCache = $zookeeperCache;
    }

    /**
     * @param string $topic
     * @param array $topicInfos
     * @return string
     */
    public function getBrokersString(string $topic, array $topicInfos)
    {
        $brokersList = array();
        foreach ($topicInfos[$topic] as $partition => $brokers) {
            foreach ($brokers['brokers'] as $broker) {
                if (in_array($broker, $brokersList) === false) {
                    $brokersList[] = $broker;
                }
            }
        }
        return implode(', ', $brokersList);
    }

    /**
     * @param string $topic
     * @return string
     */
    public function resolve(string $topic)
    {
        $topicInfos = $this->zookeeperCache->getTopic($topic);
        if ($topicInfos === null) {
            $topicInfos = $this->zookeeperApi->resolve($topic);
            $this->zookeeperCache->saveTopic($topic, $topicInfos);
        }
        return $this->getBrokersString($topic, $topicInfos);
    }
}