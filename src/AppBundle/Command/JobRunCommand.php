<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

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
        $this->setName('simpspector:job:run')
            ->addArgument('id', InputArgument::OPTIONAL, 'job id', null)
            ->addOption('no-garbage-collector', null, InputOption::VALUE_NONE);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lock = new LockHandler('simpspector:job:run');

        if (!$lock->lock()) {
            return;
        }

        $commitHandler    = $this->getContainer()->get('simpspector.app.worker.commit_handler');
        $commitRepository = $this->getContainer()->get('simpspector.app.repository.commit');

        $commits = [];

        if ($id = $input->getArgument('id')) {
            $commits[] = $commitRepository->find($id);
        } else {
            $commits = $commitRepository->findNewCommits();
        }

        foreach ($commits as $commit) {
            $output->writeln('job id ' . $commit->getId());
            $commitHandler->handle($commit);
        }

        if (!!$input->getOption('no-garbage-collector')) {
            $output->writeln('running garbage collector...');
            $garbageCollector = $this->getContainer()->get('simpspector.app.worker.garbage_collector');
            $garbageCollector->run();
        }

        $output->writeln('Job Run completed.');

        $lock->release();
    }
}
