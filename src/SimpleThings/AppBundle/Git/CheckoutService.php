<?php

namespace SimpleThings\AppBundle\Git;

use Gitlab\Client;
use GitWrapper\Event\GitLoggerListener;
use GitWrapper\GitWrapper;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use SimpleThings\AppBundle\Entity\Commit;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ProcessBuilder;

class CheckoutService
{
    private $gitlabClient;
    private $baseDir;

    /**
     * @param Client $gitlabClient
     */
    public function __construct(Client $gitlabClient, $baseDir = '/tmp')
    {
        $this->gitlabClient = $gitlabClient;
        $this->baseDir      = $baseDir;
    }

    /**
     * @param Commit $push
     * @return Checkout
     * @throws \Exception
     */
    public function create(Commit $push)
    {
        $project  = $this->gitlabClient->api('projects')->show($push->getMergeRequest()->getProject()->getRemoteId());
        $url      = $project['http_url_to_repo'];
        $revision = $push->getRevision();
        $path     = $this->getUniquePath();

        $this->cloneRepository($url, $path);
        $this->checkout($revision, $path);

        return new Checkout($url, $revision, $path);
    }

    /**
     * @param Checkout $checkout
     */
    public function remove(Checkout $checkout)
    {
        $fs = new Filesystem();
        $fs->remove($checkout->path);
    }

    /**
     * @param string $url
     * @param string $path
     * @throws \Exception
     */
    private function cloneRepository($url, $path)
    {
        $builder = new ProcessBuilder(['git', 'clone', $url, $path]);
        $process = $builder->getProcess();
        if ($process->run() !== 0) {
            throw new \Exception("cannot clone git repository: \n" . $process->getErrorOutput());
        }
    }

    /**
     * @param string $revision
     * @param string $path
     * @throws \Exception
     */
    private function checkout($revision, $path)
    {
        $builder = new ProcessBuilder(['git', 'checkout', ' --force', '--quiet', $revision]);
        $builder->setWorkingDirectory($path);
        if ($builder->getProcess()->run() !== 0) {
            throw new \Exception('cannot checkout revision');
        }
    }

    /**
     * @return string
     */
    private function getUniquePath()
    {
        return $this->baseDir . '/' . uniqid('simpspector_');
    }
} 
