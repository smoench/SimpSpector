<?php

namespace SimpleThings\AppBundle\GitLab;

use Gitlab\Api\MergeRequests;
use Gitlab\Client;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SimpleThings\AppBundle\Badge\Generator;
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
     * @var Generator
     */
    private $generator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Client $client
     * @param Generator $generator
     * @param LoggerInterface $logger
     */
    function __construct(Client $client, Generator $generator, LoggerInterface $logger = null)
    {
        $this->client    = $client;
        $this->generator = $generator;
        $this->logger    = $logger ?: new NullLogger();
    }

    /**
     * @param MergeRequest $mergeRequest
     */
    public function notify(MergeRequest $mergeRequest)
    {
        /** @var MergeRequests $mergeRequestApi */
        $mergeRequestApi = $this->client->api('merge_requests');

        $response = $mergeRequestApi->addComment(
            $mergeRequest->getProject()->getRemoteId(),
            $mergeRequest->getRemoteId(),
            $this->generator->getMarkdownForMergeRequest($mergeRequest)
        );

        $this->logger->info('notify gitlab', $response);
    }
}
