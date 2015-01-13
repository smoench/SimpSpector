<?php

namespace SimpleThings\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class JobRunCommand extends ContainerAwareCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this->setName('simpspector:job:run');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commitHandler    = $this->getContainer()->get('simple_things_app.commit_handler');
        $commitRepository = $this->getContainer()->get('simpspector.app.repository.commit');

        foreach ($commitRepository->findNewCommits() as $commit) {
            $output->writeln('job id ' . $commit->getId());
            $commitHandler->handle($commit);
        }
    }
}
