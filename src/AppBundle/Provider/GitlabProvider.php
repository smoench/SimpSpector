<?php

namespace AppBundle\Provider;

use AppBundle\Entity\MergeRequest;
use Gitlab\Client;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class GitlabProvider implements ProviderInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @param string $url
     * @param string $token
     */
    public function __construct($url, $token)
    {
        $this->client = new Client($url);
        $this->client->authenticate(Client::AUTH_HTTP_TOKEN, $token);
    }

    /**
     * @param MergeRequest $mergeRequest
     * @param string $comment
     */
    public function addMergeRequestComment(MergeRequest $mergeRequest, $comment)
    {
        $mergeRequestApi = $this->client->api('merge_requests');

        $mergeRequestApi->addComment(
            $mergeRequest->getProject()->getRemoteId(),
            $mergeRequest->getRemoteId(),
            $comment
        );
    }
}