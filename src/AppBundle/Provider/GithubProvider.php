<?php

namespace AppBundle\Provider;

use AppBundle\Entity\MergeRequest;
use Github\Api\Issue;
use Github\Api\PullRequest;
use Github\Client;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class GithubProvider implements ProviderInterface
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
        $this->client = new Client();
        $this->client->authenticate($token, null, Client::AUTH_HTTP_TOKEN);
    }

    /**
     * @param MergeRequest $mergeRequest
     * @param string $comment
     * @throws \Github\Exception\MissingArgumentException
     */
    public function addMergeRequestComment(MergeRequest $mergeRequest, $comment)
    {
        $project = $mergeRequest->getProject();

        /** @var Issue $mergeRequestApi */
        $mergeRequestApi = $this->client->api('issue');
        
        $mergeRequestApi->comments()->create(
            $project->getNamespace(),
            $project->getName(),
            $mergeRequest->getRemoteId(),
            [
                'body' => $comment
            ]
        );
    }
}