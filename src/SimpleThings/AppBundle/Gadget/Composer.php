<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Gadget;

use Composer\Factory;
use Composer\Installer;
use Composer\IO\NullIO;
use SimpleThings\AppBundle\Workspace;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Composer extends AbstractGadget
{
    /**
     * @param Workspace $workspace
     * @return mixed
     */
    public function run(Workspace $workspace)
    {
        /* first test... not working... tricky

        $composerJson = $this->getComposerJsonPath($workspace);

        if (!file_exists($composerJson)) {
            return false;
        }

        putenv('COMPOSER_CACHE_DIR=/tmp/composer');


        $io = new NullIO();

        $composer = Factory::create($io, $composerJson);
        $installer = Installer::create($io, $composer);

        $installer->run();

        return true;
        */

        $processBuilder = new ProcessBuilder(['composer', 'install', '--no-interaction']);
        $processBuilder->setWorkingDirectory($workspace->path);

        $process = $processBuilder->getProcess();
        $process->setTimeout(3600);
        $process->setEnv(['COMPOSER_HOME' => $workspace->path]);
        $process->run();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'composer';
    }

    /**
     * @param Workspace $workspace
     * @return string
     */
    private function getComposerJsonPath(Workspace $workspace)
    {
        return $workspace->path . '/composer.json';
    }
}