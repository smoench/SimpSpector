<?php

namespace SimpleThings\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class GarbageCollectorCommand extends ContainerAwareCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this->setName('simpspector:garbage-collector');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $garbageCollector = $this->getContainer()->get('simpspector.app.worker.garbage_collector');
        $garbageCollector->run();
    }
}
