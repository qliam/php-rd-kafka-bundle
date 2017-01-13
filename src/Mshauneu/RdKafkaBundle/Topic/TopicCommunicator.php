<?php

namespace Mshauneu\RdKafkaBundle\Topic;

use Mshauneu\RdKafkaBundle\Zookeeper\ZookeeperManager;
use RdKafka\Conf;
use RdKafka\TopicConf;

/**
 * TopicCommunicator
 *
 * @author Mike Shauneu <mike.shauneu@gmail.com>
 */
abstract class TopicCommunicator {

	const PARTITION_UA = -1;
	const OFFSET_BEGINNING = -2;
	const OFFSET_END = -1;
	const OFFSET_STORED = -1000;
	
	protected $brokers;
	protected $props;
	protected $topic;
	protected $topicProps;

	/**
	 * @var ZookeeperManager
	 */
	protected $zookeeperManager;

	/**
	 * @param string $brokers  
	 * @param object $props
	 * @param string $topic
	 * @param object $topicProps
	 */
	public function __construct($brokers, $props, $topic, $topicProps, $zookeeperManager) {
		$this->brokers = $brokers;
		$this->props = $props;
		$this->topic = $topic;
		$this->topicProps = $topicProps;
		$this->zookeeperManager = $zookeeperManager;
	}

	/**
	 * @param object $props
	 * @return \RdKafka\Conf
	 */
	protected function getConfig($props) {
		$conf = new Conf();
		if (null !== $props) {
			foreach ($props as $name => $value) {
				$conf->set(str_replace("_", ".", $name), $value);
			}
		}
		return $conf;
	}
	
	/**
	 * @param object $props
	 * @return \RdKafka\TopicConf
	 */
	protected function getTopicConfig($props) {
		$topicConf = new TopicConf();
		if (null !== $props) {
			foreach ($props as $name => $value) {
				$topicConf->set(str_replace("_", ".", $name), $value);
			}
		}
		return $topicConf;
	}
	
}