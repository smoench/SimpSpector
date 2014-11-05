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
    private $username;
    private $password;
    private $baseDir;

    /**
     * @param Client $gitlabClient
     */
    public function __construct(Client $gitlabClient, $username, $password, $baseDir = '/tmp')
    {
        $this->gitlabClient = $gitlabClient;
        $this->username     = $username;
        $this->password     = $password;
        $this->baseDir      = $baseDir;
    }

    /**
     * @param Commit $push
     * @return Checkout
     * @throws \Exception
     */
    public function create(Commit $push)
    {
        $project = $this->gitlabClient->api('projects')->show($push->getMergeRequest()->getProject()->getRemoteId());
        $url     = $this->modifyGitUrl($project['http_url_to_repo']);

        $revision = $push->getRevision();
        $path     = $this->getUniquePath();

        $git = $this->createGitWrapper();

        $workingspace = $git->cloneRepository($url, $path);
        $workingspace->checkout($revision);

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
     * @return string
     */
    private function getUniquePath()
    {
        return $this->baseDir . '/' . uniqid();
    }

    /**
     * @return GitWrapper
     */
    private function createGitWrapper()
    {
        $root = realpath(__DIR__ . '/../../../..');

        $wrapper = new GitWrapper();

        $log = new Logger('git');
        $log->pushHandler(new StreamHandler($root . '/var/logs/git.log', Logger::DEBUG));

        $listener = new GitLoggerListener($log);
        $wrapper->addLoggerListener($listener);

        return $wrapper;
    }

    /**
     * @param $url
     * @return string
     */
    private function modifyGitUrl($url)
    {
        return str_replace(
            'https://',
            sprintf('https://%s:%s@', $this->username, $this->password),
            $url
        );
    }
} 
