<?php

namespace SimpleThings\AppBundle\Process;

use SimpleThings\AppBundle\Logger\AbstractLogger;
use SimpleThings\AppBundle\Logger\NullLogger;
use Symfony\Component\Process\ProcessBuilder as BaseProcessBuilder;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class ProcessBuilder extends BaseProcessBuilder
{
    /**
     * @param AbstractLogger $logger
     * @return string
     */
    public function run(AbstractLogger $logger = null)
    {
        $logger = $logger ?: new NullLogger();

        $process = $this->getProcess();
        $process->setTimeout(3600);

        $logger->writeln();
        $logger->writeln('CMD > ' . $process->getCommandLine());
        $logger->writeln();

        $process->run(
            function ($type, $buffer) use ($logger) {
                $logger->write($buffer);
            }
        );

        return $process->getOutput();
    }
} 