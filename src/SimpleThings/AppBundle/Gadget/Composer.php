<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Gadget;

use SimpleThings\AppBundle\Workspace;
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
     * @return mixed
     */
    public function run(Workspace $workspace)
    {
        if (!isset($workspace->config['composer'])) {
            return;
        }

        $processBuilder = new ProcessBuilder(['composer', 'install', '--no-interaction']);
        $processBuilder->setWorkingDirectory($workspace->path);

        $process = $processBuilder->getProcess();
        $process->setTimeout(3600);
        $process->setEnv(['COMPOSER_HOME' => $this->composerHome]);
        $process->run();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'composer';
    }
}