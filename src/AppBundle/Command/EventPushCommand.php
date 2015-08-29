<?php
/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

use DavidBadura\GitWebhooks\Event\PushEvent;
use DavidBadura\GitWebhooks\Struct\Commit;
use DavidBadura\GitWebhooks\Struct\Repository;

class EventPushCommand extends ContainerAwareCommand
{
    private $input;
    private $output;
    private $questionHelper;

    protected function configure()
    {
        $this
            ->setName('simpspector:event:push')
            ->addOption('project', null, InputOption::VALUE_OPTIONAL, 'project name', null)
            ->addOption('project-id', null, InputOption::VALUE_OPTIONAL, 'project id', 'test-9999')
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
        $this->input          = $input;
        $this->output         = $output;
        $this->questionHelper = $this->getHelper('question');

        $handler = $this
            ->getContainer()
            ->get('simpspector.app.webhook.handler');

        $url        = $this->getOption('url', 'repository url', null);
        $commitHash = $this->getOption('commit', 'commit hash of last commit', null);
        $project    = $this->getOption('project', 'project name', null);
        $projectId  = $input->getOption('project-id');

        $event             = new PushEvent();
        $event->type       = PushEvent::TYPE_BRANCH;
        $event->branchName = 'master';
        $event->repository = new Repository();

        $event->repository->id   = $projectId;
        $event->repository->url  = $url;
        $event->repository->name = $project;

        $commit          = new Commit();
        $commit->id      = $commitHash;
        $commit->message = "Test-Data for Commit " . $commitHash;
        $commit->date    = new \DateTime(); // todo correct timestamp

        $event->commits = [$commit];

        $handler->handle($event);
    }

    private function getOption($key, $label, $description)
    {
        $option = $this->input->getOption($key);

        if (! empty($option)) {
            return $option;
        }

        return $this->questionHelper->ask(
            $this->input,
            $this->output,
            new Question("$label: ", null)
        );
    }
}
