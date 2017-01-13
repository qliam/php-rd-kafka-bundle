<?php
/**
 * Created by PhpStorm.
 * User: Victor
 * Date: 13/01/2017
 * Time: 09:43
 */

namespace Mshauneu\RdKafkaBundle\Zookeeper;


use Monolog\Logger;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\Container;

class ZookeeperCache
{
    /**
     * @var CacheItemInterface
     */
    protected $cache;

    function __construct()
    {
        $this->cache = new FilesystemAdapter();
    }

    /**
     * @param string $topicName
     * @return array
     */
    public function getTopic(string $topicName)
    {
        $topicCache = $this->cache->getItem('kafka.topic.' . $topicName);
        if ($topicCache->isHit() === false) {
            return null;
        }
        $topic = json_decode($topicCache->get(), true);
        return $topic;
    }

    public function saveTopic(string $topicName, $topicInfos)
    {
        $topicCache = $this->cache->getItem('kafka.topic.' . $topicName);
        $topicCache->set(json_encode($topicInfos));
        $this->cache->save($topicCache);
    }
}