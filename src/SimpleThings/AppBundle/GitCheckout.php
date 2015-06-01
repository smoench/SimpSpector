<?php

namespace SimpleThings\AppBundle;

use SimpleThings\AppBundle\Entity\Commit;
use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Logger\NullLogger;
use SimpSpector\Analyser\Process\ProcessBuilder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class GitCheckout
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
     * @param AbstractLogger $logger
     * @return Workspace
     */
    public function create(Commit $commit, AbstractLogger $logger = null)
    {
        $logger = $logger ?: new NullLogger();

        $workspace = $this->createWorkspace($commit);
        $this->gitClone($workspace, $logger);
        $this->gitCheckout($workspace, $logger);

        $workspace->path = realpath($workspace->path);

        return $workspace;
    }

    /**
     * @param Workspace $workspace
     */
    public function remove(Workspace $workspace)
    {
        $fs = new Filesystem();
        $fs->remove($workspace->path);
    }

    /**
     * @param Commit $commit
     * @return Workspace
     */
    private function createWorkspace(Commit $commit)
    {
        $workspace           = new Workspace();
        $workspace->url      = $commit->getProject()->getRepositoryUrl();
        $workspace->revision = $commit->getRevision();
        $workspace->path     = $this->baseDir . '/' . $commit->getUniqueId();

        if (file_exists($workspace->path)) {
            $this->remove($workspace);
        }

        return $workspace;
    }

    /**
     * @param Workspace $workspace
     * @param AbstractLogger $logger
     */
    private function gitClone(Workspace $workspace, AbstractLogger $logger)
    {
        $processBuilder = new ProcessBuilder([
            'git',
            'clone',
            $workspace->url,
            basename($workspace->path)
        ]);
        $processBuilder->setWorkingDirectory(dirname($workspace->path));
        $processBuilder->run($logger);
    }

    /**
     * @param Workspace $workspace
     * @param AbstractLogger $logger
     */
    private function gitCheckout(Workspace $workspace, AbstractLogger $logger)
    {
        $processBuilder = new ProcessBuilder([
            'git',
            'checkout',
            $workspace->revision
        ]);
        $processBuilder->setWorkingDirectory($workspace->path);
        $processBuilder->run($logger);
    }
}
