<?php

namespace AppBundle\Worker;

use AppBundle\Entity\Commit;
use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Logger\NullLogger;
use SimpSpector\Analyser\Process\ProcessBuilder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class WorkspaceManager
{
    /**
     * @var string
     */
    private $baseDir;

    /**
     * @param string $baseDir
     */
    public function __construct($baseDir = '/tmp')
    {
        $this->baseDir = $baseDir;
    }

    /**
     * @param Commit $commit
     * @return string
     */
    public function path(Commit $commit)
    {
        return $this->baseDir . '/' . $commit->getUniqueId();
    }

    /**
     * @param Commit $commit
     * @param AbstractLogger $logger
     * @return string
     */
    public function create(Commit $commit, AbstractLogger $logger = null)
    {
        $logger = $logger ?: new NullLogger();

        $this->cleanUp($commit);

        $path     = $this->path($commit);
        $url      = $commit->getProject()->getRepositoryUrl();
        $revision = $commit->getRevision();

        $this->gitClone($path, $url, $logger);
        $this->gitCheckout($path, $revision, $logger);

        return realpath($path);
    }

    /**
     * @param Commit $commit
     */
    public function cleanUp(Commit $commit)
    {
        $path = $this->path($commit);
        $fs   = new Filesystem();

        if ($fs->exists($path)) {
            $fs->remove($path);
        }
    }

    /**
     * @param string $path
     * @param string $url
     * @param AbstractLogger $logger
     */
    private function gitClone($path, $url, AbstractLogger $logger)
    {
        $processBuilder = new ProcessBuilder([
            'git',
            'clone',
            $url,
            basename($path)
        ]);

        $processBuilder->setWorkingDirectory(dirname($path));
        $processBuilder->run($logger);
    }

    /**
     * @param string $path
     * @param string $revision
     * @param AbstractLogger $logger
     */
    private function gitCheckout($path, $revision, AbstractLogger $logger)
    {
        $processBuilder = new ProcessBuilder([
            'git',
            'checkout',
            $revision
        ]);

        $processBuilder->setWorkingDirectory($path);
        $processBuilder->run($logger);
    }
}
