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

class EventPushCommand extends ContainerAwareCommand
{
    private $input;
    private $output;
    private $questionHelper;

    protected function configure()
    {
        $this->setName('simpspector:event:push')
            ->addOption('project', 'project name', InputOption::VALUE_OPTIONAL, null)
            ->addOption('url', 'repository url', InputOption::VALUE_OPTIONAL, null)
            ->addOption('commit-id', 'commit-hash of last commit', InputOption::VALUE_OPTIONAL, '')
        ;
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

        $project  = $this->getOption('project', 'project', null);
        $url      = $this->getOption('url', 'repository url', null);
        $commitId = $this->getOption('commit-id', 'commit hash of last commit', null);
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
