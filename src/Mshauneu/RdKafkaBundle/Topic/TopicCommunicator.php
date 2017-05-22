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
		// We can change the amount of time a socket blocking operation lasts for, which will help librdkafka release your application to continue
		$conf->set('socket.blocking.max.ms', 1);
		// we can also set the buffering time, so we dispatch asap:
		$conf->set('queue.buffering.max.ms', 1);
		// Finally, we can say to only wait for X messages before sending to Kafka. In a lightweight / custom app, you may only be sending one message, so get it out there straight away
		$conf->set('queue.buffering.max.messages', 10);
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
