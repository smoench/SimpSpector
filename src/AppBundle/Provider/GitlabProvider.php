<?php

namespace AppBundle\Provider;

use AppBundle\Provider\Struct\MergeRequest;
use AppBundle\Provider\Struct\Project;
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
     * @param int $projectId
     * @param int $mergeRequestId
     * @param string $comment
     */
    public function addMergeRequestComment($projectId, $mergeRequestId, $comment)
    {
        $mergeRequestApi = $this->client->api('merge_requests');
        $mergeRequestApi->addComment($projectId, $mergeRequestId, $comment);
    }

    /**
     * @param int $projectId
     * @return Project
     */
    public function getProjectInformation($projectId)
    {
        $data = $this->client->api('project')->show($projectId);

        $project = new Project();

        return $project;
    }

    /**
     * @param int $projectId
     * @param int $mergeRequestId
     * @return MergeRequest
     */
    public function getMergeRequestInformation($projectId, $mergeRequestId)
    {
        $data = $this->client->api('mr')->show($projectId, $mergeRequestId);

        $mergeRequest = new MergeRequest();

        return $mergeRequest;
    }
}