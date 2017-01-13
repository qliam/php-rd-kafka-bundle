<?php
/**
 * Created by PhpStorm.
 * User: Victor
 * Date: 13/01/2017
 * Time: 14:09
 */

namespace Mshauneu\RdKafkaBundle\Command\Zookeeper;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteCacheCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('zookeeper:cache:delete')
            ->addOption('topic', null, InputOption::VALUE_REQUIRED, 'Topic cache to delete');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $topic = $input->getOption('topic');
        $topicSummary = $this->getContainer()->get('mshauneuu_rd_zookeeper_cache')->getTopic($topic);
        if ($topicSummary === null) {
            $output->writeln(sprintf('No cache for topic %s', $topic));
        } else {
            $this->getContainer()->get('mshauneuu_rd_zookeeper_cache')->deleteTopic($topic);
            $output->writeln(sprintf('Cache for topic \'%s\' deleted', $topic));
        }
    }
}