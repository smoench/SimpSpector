<?php
/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */

namespace AppBundle\Command;

use AppBundle\Fixture;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use DavidBadura\GitWebhooks\Event\PushEvent;
use DavidBadura\GitWebhooks\Struct\Commit;

class EventPushBranchCommand extends AbstractCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('simpspector:event:push-branch')
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

        $event->commits = [$helper->generateCommit($commitHash)];

        $this->handleEvent($event);
    }
}
