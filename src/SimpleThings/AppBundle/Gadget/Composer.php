<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Gadget;

use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Logger\AbstractLogger;
use SimpleThings\AppBundle\Workspace;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Composer extends AbstractGadget
{
    const NAME = 'composer';

    /**
     * @var string
     */
    private $composerHome;

    /**
     * @param string $composerHome
     */
    public function __construct($composerHome = '/tmp/.composer')
    {
        $this->composerHome = $composerHome;
    }

    /**
     * @param Workspace      $workspace
     * @param AbstractLogger $logger
     * @return Issue[]
     */
    public function run(Workspace $workspace, AbstractLogger $logger)
    {
        $processBuilder = new ProcessBuilder(
            [
                'composer',
                'install',
                '--no-interaction',
                '--no-scripts',
                '--no-plugins'
            ]
        );

        $processBuilder->setWorkingDirectory($workspace->path);

        $process = $processBuilder->getProcess();
        $process->setTimeout(3600);
        $process->setEnv(['COMPOSER_HOME' => $this->composerHome]);

        $process->run(
            function ($type, $buffer) use ($logger) {
                $logger->write($buffer);
            }
        );

        if ($process->getExitCode() === 0) {
            return [];
        }

        $issue = new Issue('you have a composer problem', self::NAME, Issue::LEVEL_CRITICAL);
        $issue->setFile('composer.json');
        $issue->setExtraInformation(
            [
                'output'      => $process->getOutput(),
                'errorOutput' => $process->getErrorOutput()
            ]
        );

        return [$issue];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return 100;
    }
}
