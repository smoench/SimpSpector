<?php

namespace SimpleThings\AppBundle\GitLab;

use Gitlab\Client;
use SimpleThings\AppBundle\ButtonGenerator;
use SimpleThings\AppBundle\Entity\Commit;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Notifier
{
    /**
     * @var Client;
     */
    private $client;

    /**
     * @var ButtonGenerator
     */
    private $generator;

    /**
     * @param Client $client
     */
    function __construct(Client $client, ButtonGenerator $generator)
    {
        $this->client = $client;
        $this->generator = $generator;
    }

    /**
     * @param Commit $push
     */
    public function notify(Commit $push)
    {
        $mergeRequest = $push->getMergeRequest();

        $this->client->api('merge_requests')->addComment(
            $mergeRequest->getProject()->getRemoteId(),
            $mergeRequest->getRemoteId(),
            $this->generator->generate($mergeRequest)
        );
    }
}