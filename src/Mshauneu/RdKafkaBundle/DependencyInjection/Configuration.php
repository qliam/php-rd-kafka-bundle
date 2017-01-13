<?php

namespace Mshauneu\RdKafkaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;


/**
 * Configuration
 * 
 * @author Mike Shauneu <mike.shauneu@gmail.com>
 */
class Configuration implements ConfigurationInterface {
	
	use CommunicatorConfiguration;
	use TopicProducerConfiguration;
	use TopicConsumerConfiguration;
	
	/**
	 * {@inheritDoc}
	 * @see ConfigurationInterface::getConfigTreeBuilder()
	 */
	public function getConfigTreeBuilder() {
		$tree = new TreeBuilder();
		$rootNode = $tree->root('mshauneu_rd_kafka');
		$rootNode
			->children()
				->scalarNode('zookeeper')->defaultNull()->end()
				->integerNode('zookeeper_cache_lifetime')->defaultValue(500)->end()
				->arrayNode('producers')
					->canBeUnset()
					->prototype('array')
						->children()
							->scalarNode('brokers')->defaultNull()->end()
							->scalarNode('topic')->isRequired()->end()
							->append($this->getPropertiesNodeDef())
							->append($this->getTopicProducerPropertiesNodeDef())
						->end()
					->end()
				->end()

				->arrayNode('consumers')
					->canBeUnset()
					->prototype('array')
						->children()
							->scalarNode('brokers')->defaultNull()->end()
							->scalarNode('topic')->isRequired()->end()
							->append($this->getPropertiesNodeDef())
							->append($this->getTopicConsumerPropertiesNodeDef())
						->end()
					->end()
				->end()
			->end()
		;
		
		return $tree;
	}
	
}
