<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Gadget;

use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Workspace;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Composer extends AbstractGadget
{
    /**
     * @var string
     */
    private $composerHome;

    /**
     * @param string $composerHome
     */
    function __construct($composerHome = '/tmp/.composer')
    {
        $this->composerHome = $composerHome;
    }

    /**
     * @param Workspace $workspace
     * @throws \Exception
     * @return Issue[]
     */
    public function run(Workspace $workspace)
    {
        $processBuilder = new ProcessBuilder([
            'composer',
            'install',
            '--no-interaction',
            '--no-scripts',
            '--no-plugins'
        ]);

        $processBuilder->setWorkingDirectory($workspace->path);

        $process = $processBuilder->getProcess();
        $process->setTimeout(3600);
        $process->setEnv(['COMPOSER_HOME' => $this->composerHome]);

        if ($process->run() === 0) {
            return [];
        }

        $issue = new Issue('you have a composer problem', 'composer', Issue::LEVEL_CRITICAL);
        $issue->setFile('composer.json');
        $issue->setExtraInformation([
            'output'      => $process->getOutput(),
            'errorOutput' => $process->getErrorOutput()
        ]);

        return [$issue];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'composer';
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return 100;
    }
}
