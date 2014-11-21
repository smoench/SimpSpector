<?php

namespace SimpleThings\AppBundle;

use Gitlab\Client;
use GitWrapper\GitWorkingCopy;
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
     * @return GitWorkingCopy
     * @throws \Exception
     */
    public function create(Commit $commit)
    {
        $project = $this->gitlabClient->api('projects')->show($commit->getMergeRequest()->getProject()->getRemoteId());
        $url = $project['ssh_url_to_repo'];
        $revision = $commit->getRevision();
        $path = $this->getUniquePath();

        $workingCopy = $this->gitWrapper->cloneRepository($url, $path);
        $workingCopy->checkout($revision);

        return $workingCopy;
    }

    /**
     * @param GitWorkingCopy $workingCopy
     */
    public function remove(GitWorkingCopy $workingCopy)
    {
        $fs = new Filesystem();
        $fs->remove($workingCopy->getDirectory());
    }

    /**
     * @return string
     */
    private function getUniquePath()
    {
        return $this->baseDir . '/' . uniqid();
    }
} 
