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

    /**
     * ZookeeperCache constructor.
     */
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
        $topicCache = $this->cache->getItem($this->getItemName($topicName));
        if ($topicCache->isHit() === false) {
            return null;
        }
        $topic = json_decode($topicCache->get(), true);
        return $topic;
    }

    /**
     * @param string $topicName
     * @param array $topicInfos
     */
    public function saveTopic(string $topicName, array $topicInfos)
    {
        $topicCache = $this->cache->getItem($this->getItemName($topicName));
        $topicInfos['timestamp'] = time();
        $topicCache->set(json_encode($topicInfos));
        $this->cache->save($topicCache);
    }

    /**
     * @param string $topicName
     */
    public function deleteTopic(string $topicName)
    {
        $this->cache->deleteItem($this->getItemName($topicName));
    }

    /**
     * @param string $topicName
     * @return string
     */
    protected function getItemName(string $topicName)
    {
        return 'kafka.topic.' . $topicName;
    }
}