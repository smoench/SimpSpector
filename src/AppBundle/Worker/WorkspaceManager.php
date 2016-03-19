<?php

namespace AppBundle\Worker;

use AppBundle\Entity\Commit;
use AppBundle\Entity\MergeRequest;
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

        //$this->cleanUp($commit);

        $path     = $this->path($commit);
        $url      = $commit->getGitRepository();
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
     * @param MergeRequest $mergeRequest
     * @param string $path path to git checkout
     *
     * @return string hash of base commit from target branch
     */
    public function getBaseCommit(MergeRequest $mergeRequest, Commit $commit, $path, AbstractLogger $logger)
    {
        if ($mergeRequest->getTargetBranch() === $mergeRequest->getSourceBranch()) {
            return null;
        }

        $processBuilder = new \Symfony\Component\Process\ProcessBuilder([
            'git',
            'merge-base',
            'origin/' . $mergeRequest->getTargetBranch(),
            'origin/' . $mergeRequest->getSourceBranch(),
        ]);

        $processBuilder->setWorkingDirectory($path);
        $process = $processBuilder->getProcess();
        $process->run(
            function ($type, $buffer) use ($logger) {
                $logger->write($buffer);
            }
        );

        if (! $process->isSuccessful()) {
            $logger->write("process did not finish sucessfully");
            return null;
        }

        return trim($process->getOutput());
    }

    /**
     * @param string $path
     * @param string $url
     * @param AbstractLogger $logger
     */
    private function gitClone($path, $url, AbstractLogger $logger)
    {
        $fs = new Filesystem();
        if (! $fs->exists($this->baseDir)) {
            $fs->mkdir($this->baseDir);
        }

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
