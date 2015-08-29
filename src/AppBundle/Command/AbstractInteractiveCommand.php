<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

abstract class AbstractInteractiveCommand extends ContainerAwareCommand
{
    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var QuestionHelper
     */
    protected $questionHelper;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input          = $input;
        $this->output         = $output;
        $this->questionHelper = $this->getHelper('question');
    }

    /**
     * @param string $key
     * @param string $label
     *
     * @return string
     */
    protected function getOption($key, $label)
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
}
