<?php
/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */

namespace AppBundle\Command;

use AppBundle\WebhookHandler;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Logger\ConsoleLogger;

use DavidBadura\GitWebhooks\Event\PushEvent;
use DavidBadura\GitWebhooks\Struct\Commit;
use DavidBadura\GitWebhooks\Struct\Repository;

class EventPushCommand extends ContainerAwareCommand
{
    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var QuestionHelper
     */
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

        $logger = new ConsoleLogger($output);

        $handler = $this
            ->getContainer()
            ->get('simpspector.app.webhook.handler');
        $handler->setLogger($logger);

        $url        = $this->getOption('url', 'repository url');
        $commitHash = $this->getOption('commit', 'commit hash of last commit');
        $project    = $this->getOption('project', 'project name');
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
        $commit->message = 'Test-Data for Commit ' . $commitHash;
        $commit->date    = new \DateTime(); // todo correct timestamp

        $event->commits = [$commit];

        $this->getHandler()
             ->handle($event);
    }

    /**
     * @param string $key
     * @param string $label
     *
     * @return string
     */
    private function getOption($key, $label)
    {
        $option = $this->input->getOption($key);

        if (! empty($option)) {
            return $option;
        }

        return $this->questionHelper->ask(
            $this->input,
            $this->output,
            new Question($label . ': ', null)
        );
    }

    /**
     * @return WebhookHandler
     */
    protected function getHandler()
    {
        return $this
            ->getContainer()
            ->get('simpspector.app.webhook.handler');
    }
}
