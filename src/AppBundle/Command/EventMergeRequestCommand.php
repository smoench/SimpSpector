<?php

namespace AppBundle\Command;

use AppBundle\Fixture;
use DavidBadura\GitWebhooks\Event\MergeRequestEvent;
use DavidBadura\GitWebhooks\Struct\Commit;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EventMergeRequestCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('simpspector:event:merge-request')
            ->addOption('url', null, InputOption::VALUE_OPTIONAL, 'repository url', null)
            ->addOption('id', null, InputOption::VALUE_OPTIONAL, 'merge request id', rand(10000, 99999))
            ->addOption('commit', null, InputOption::VALUE_OPTIONAL, 'commit-hash of last commit', '')
            ->addOption('from-branch', null, InputOption::VALUE_OPTIONAL, '', 'MR source branch, e.g. feature branch')
            ->addOption('to-branch', null, InputOption::VALUE_OPTIONAL, 'target of the MR, e.g. master/stable/...', '')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $helper = new Fixture\Helper();

        $url        = $this->getOption('url', 'repository url');
        $commitHash = $this->getOption('commit', 'commit hash of last commit');
        $fromBranch = $this->getOption('from-branch', '');
        $toBranch   = $this->getOption('to-branch', '');

        $event = new MergeRequestEvent();

        $event->id               = $this->getOption('id', '');
        $event->title            = 'Test Pull-Request ' . $this->getOption('id', '');
        $event->repository       = $helper->generateRepositoryByUrl($url);
        $event->targetBranch     = $toBranch;
        $event->sourceRepository = $helper->generateRepositoryByUrl($url);
        $event->sourceBranch     = $fromBranch;
        $event->state            = MergeRequestEvent::STATE_OPENED;
        $event->createdAt        = new \DateTime();
        $event->updatedAt        = new \DateTime();

        $event->lastCommit = $helper->generateCommit($commitHash);

        $this->handleEvent($event);
    }
}
