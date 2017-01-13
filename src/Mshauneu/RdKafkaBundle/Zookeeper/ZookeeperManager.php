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
     * Cache time in sec
     *
     * @var int
     */
    protected $cacheLifetime;

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
     * @param int $cacheLifetime
     */
    public function setCacheLifetime(int $cacheLifetime)
    {
        $this->cacheLifetime = $cacheLifetime;
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
        $topicSummary = $this->zookeeperCache->getTopic($topic);

        if ($topicSummary !== null) {
            if (($topicSummary['timestamp'] + $this->cacheLifetime) < time()) {
                $topicInfos = $this->zookeeperApi->resolveTopic($topic);
                if ($topicInfos['version'] != $topicSummary['version']) {
                    $this->zookeeperCache->deleteTopic($topic);
                    $topicSummary = $this->zookeeperApi->resolveBrokers($topic, $topicInfos);
                    $this->zookeeperCache->saveTopic($topic, $topicSummary);
                }
            }
        } else {
            $topicInfos = $this->zookeeperApi->resolveTopic($topic);
            $topicSummary = $this->zookeeperApi->resolveBrokers($topic, $topicInfos);
            $this->zookeeperCache->saveTopic($topic, $topicSummary);
        }
        return $this->getBrokersString($topic, $topicSummary);
    }
}