<?php

namespace SimpleThings\AppBundle\GitLab;

use Gitlab\Client;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SimpleThings\AppBundle\ButtonGenerator;
use SimpleThings\AppBundle\Entity\MergeRequest;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Client $client
     * @param ButtonGenerator $generator
     * @param LoggerInterface $logger
     */
    function __construct(Client $client, ButtonGenerator $generator, LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->generator = $generator;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * @param MergeRequest $mergeRequest
     */
    public function notify(MergeRequest $mergeRequest)
    {
        $response = $this->client->api('merge_requests')->addComment(
            $mergeRequest->getProject()->getRemoteId(),
            $mergeRequest->getRemoteId(),
            $this->generator->generate($mergeRequest)
        );

        $this->logger->info('notify gitlab', $response);
    }
}