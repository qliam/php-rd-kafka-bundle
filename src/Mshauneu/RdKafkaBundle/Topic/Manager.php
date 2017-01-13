<?php

namespace Mshauneu\RdKafkaBundle\Topic;
use Mshauneu\RdKafkaBundle\Zookeeper\ZookeeperManager;


/**
 * Manager
 *
 * @author Mike Shauneu <mike.shauneu@gmail.com>
 */
class Manager {	
	
	/**
	 * @var Producer[]
	 */
	protected $producers = array();
	
	/**
	 * @var Consumer[]
	 */
	protected $consumers = array();

	/**
	 * @var ZookeeperManager
	 */
	protected $zookeeperManager;

	/**
	 * Manager constructor.
	 * @param ZookeeperManager $zookeeperManager
	 */
	function __construct(ZookeeperManager $zookeeperManager)
	{
		$this->zookeeperManager = $zookeeperManager;
	}

	/**
	 * @param string $name	
	 * @param $props
	 */
	public function addProducer($name, $brokers, $props, $topic, $topicProps) {
		$this->producers[$name] = new TopicProducer($brokers, $props, $topic, $topicProps, $this->zookeeperManager);
	}

	/**
	 * @param string $name 
	 * @return TopicProducer
	 */
	public function getProducer($name) {
		return array_key_exists($name, $this->producers) ? $this->producers[$name] : null;
	}	

	/**
	 * @param string $name
	 * @param $props
	 */
	public function addConsumer($name, $brokers, $props, $topic, $topicProps) {
		$this->consumers[$name] = new TopicConsumer($brokers, $props, $topic, $topicProps, $this->zookeeperManager);
	}
	
	
	/**
	 * @param $name
	 * @return TopicConsumer
	 */
	public function getConsumer($name) {
		return array_key_exists($name, $this->consumers) ? $this->consumers[$name] : null;
	}
	
}