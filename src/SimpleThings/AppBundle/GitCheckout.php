<?php

namespace SimpleThings\AppBundle;

use Gitlab\Client;
use GitWrapper\GitWrapper;
use SimpleThings\AppBundle\Entity\Commit;
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
     * @var GitWrapper
     */
    private $gitWrapper;

    /**
     * @param GitWrapper $gitWrapper
     * @param string $baseDir
     */
    public function __construct(GitWrapper $gitWrapper, $baseDir = '/tmp')
    {
        $this->gitWrapper = $gitWrapper;
        $this->baseDir    = $baseDir;
    }

    /**
     * @param Commit $commit
     * @return Workspace
     * @throws \Exception
     */
    public function create(Commit $commit)
    {
        $workspace           = new Workspace();
        $workspace->revision = $commit->getRevision();
        $workspace->path     = $this->baseDir . '/' . $this->createFolderName($commit);

        if (file_exists($workspace->path)) {
            $this->remove($workspace);
        }

        $workingCopy = $this->gitWrapper->cloneRepository(
            $commit->getMergeRequest()->getProject()->getRepositoryUrl(),
            $workspace->path
        );

        $workingCopy->checkout($workspace->revision);

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
     * @return string
     */
    private function createFolderName(Commit $commit)
    {
        return sprintf(
            "%s_%s_%s",
            $commit->getMergeRequest()->getProject()->getId(),
            $commit->getMergeRequest()->getId(),
            $commit->getRevision()
        );
    }
} 
