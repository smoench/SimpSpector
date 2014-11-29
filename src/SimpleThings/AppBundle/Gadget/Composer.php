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
     */
    public function prepare(Workspace $workspace)
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

        if ($process->run() !== 0) {
            throw new \Exception($process->getErrorOutput());
        }
    }

    /**
     * @param Workspace $workspace
     * @return Issue[]
     */
    public function run(Workspace $workspace)
    {
        return [];
    }

    /**
     * @param Workspace $workspace
     */
    public function cleanup(Workspace $workspace)
    {
        $fs = new Filesystem();
        $fs->remove($workspace->path . '/vendor');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'composer';
    }
}