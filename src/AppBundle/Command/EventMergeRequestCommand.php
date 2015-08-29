<?php

namespace AppBundle\Command;

use DavidBadura\GitWebhooks\Event\MergeRequestEvent;
use DavidBadura\GitWebhooks\Struct\Commit;
use DavidBadura\GitWebhooks\Struct\Repository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class EventMergeRequestCommand extends AbstractInteractiveCommand
{
    protected function configure()
    {
        $this
            ->setName('simpspector:event:merge-request')
            ->addOption('project', null, InputOption::VALUE_OPTIONAL, 'project name', null)
            ->addOption('project-id', null, InputOption::VALUE_OPTIONAL, 'project id', 'test-1337')
            ->addOption('url', null, InputOption::VALUE_OPTIONAL, 'repository url', null)
            ->addOption('commit', null, InputOption::VALUE_OPTIONAL, 'commit-hash of last commit', '');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $url        = $this->getOption('url', 'repository url');
        $commitHash = $this->getOption('commit', 'commit hash of last commit');
        $project    = $this->getOption('project', 'project name');
        $projectId  = $input->getOption('project-id');

        $event = new MergeRequestEvent();

        $event->id               = rand(10000, 99999);
        $event->title            = 'Test Pull-Request ' . rand(10000, 99999);
        $event->repository       = new Repository();
        $event->targetBranch     = 'master';
        $event->sourceRepository = new Repository();
        $event->sourceBranch     = 'test/pull-request';
        $event->state            = MergeRequestEvent::STATE_OPENED;
        $event->createdAt        = new \DateTime();
        $event->updatedAt        = new \DateTime();

        $event->repository->id   = $projectId;
        $event->repository->url  = $url;
        $event->repository->name = $project;

        $event->sourceRepository->id   = $projectId;
        $event->sourceRepository->url  = $url;
        $event->sourceRepository->name = $project;

        $commit          = new Commit();
        $commit->id      = $commitHash;
        $commit->message = 'Test-Data for Commit ' . $commitHash;
        $commit->date    = new \DateTime('-4days');

        $event->lastCommit = $commit;

        /** @var WebhookHandler $handler */
        $handler = $this->getContainer()->get('simpspector.app.webhook.handler');
        $handler->setLogger(new ConsoleLogger($output, [LogLevel::INFO => OutputInterface::VERBOSITY_NORMAL]));
        $handler = $this->getHandler();
        $handler->handle($event);
    }
}
