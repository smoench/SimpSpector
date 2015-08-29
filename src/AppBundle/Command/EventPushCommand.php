<?php
/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */

namespace AppBundle\Command;

use AppBundle\Fixture;
use AppBundle\WebhookHandler;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use DavidBadura\GitWebhooks\Event\PushEvent;
use DavidBadura\GitWebhooks\Struct\Commit;
use DavidBadura\GitWebhooks\Struct\Repository;

class EventPushCommand extends AbstractInteractiveCommand
{
    protected function configure()
    {
        $this
            ->setName('simpspector:event:push')
            ->addOption('url', null, InputOption::VALUE_OPTIONAL, 'repository url', null)
            ->addOption('commit', null, InputOption::VALUE_OPTIONAL, 'commit-hash of last commit', '');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $helper = new Fixture\Helper();

        $url        = $this->getOption('url', 'repository url');
        $commitHash = $this->getOption('commit', 'commit hash of last commit');

        $event             = new PushEvent();
        $event->type       = PushEvent::TYPE_BRANCH;
        $event->branchName = 'master';
        $event->repository = $helper->generateRepositoryByUrl($url);

        $commit          = new Commit();
        $commit->id      = $commitHash;
        $commit->message = 'Test-Data for Commit ' . $commitHash;
        $commit->date    = new \DateTime(); // todo correct timestamp

        $event->commits = [$commit];

        /** @var WebhookHandler $handler */
        $handler = $this->getContainer()->get('simpspector.app.webhook.handler');
        $handler->setLogger(new ConsoleLogger($output, [LogLevel::INFO => OutputInterface::VERBOSITY_NORMAL]));
        $handler->handle($event);
    }
}
