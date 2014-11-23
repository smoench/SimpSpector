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
     * @var Client
     */
    private $gitlabClient;

    /**
     * @var string
     */
    private $baseDir;

    /**
     * @var GitWrapper
     */
    private $gitWrapper;

    /**
     * @param Client $gitlabClient
     * @param GitWrapper $gitWrapper
     * @param string $baseDir
     */
    public function __construct(Client $gitlabClient, GitWrapper $gitWrapper, $baseDir = '/tmp')
    {
        $this->gitlabClient = $gitlabClient;
        $this->gitWrapper = $gitWrapper;
        $this->baseDir = $baseDir;
    }

    /**
     * @param Commit $commit
     * @return Workspace
     * @throws \Exception
     */
    public function create(Commit $commit)
    {
        $workspace = new Workspace();

        $project = $this->gitlabClient->api('projects')->show($commit->getMergeRequest()->getProject()->getRemoteId());
        $url = $project['ssh_url_to_repo'];
        $revision = $commit->getRevision();
        $workspace->path = $this->getUniquePath();

        $workingCopy = $this->gitWrapper->cloneRepository($url, $workspace->path);
        $workingCopy->checkout($revision);

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
    private function getUniquePath()
    {
        return $this->baseDir . '/' . uniqid();
    }
} 
