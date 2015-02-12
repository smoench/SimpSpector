<?php

namespace SimpSpector\Provider;

use Gitlab\Api\MergeRequests;
use Gitlab\Client;
use SimpleThings\AppBundle\Badge\Generator;
use SimpleThings\AppBundle\Entity\MergeRequest;
use SimpleThings\AppBundle\Entity\Project;
use SimpSpector\Provider\Event\MergeRequestEvent;
use SimpSpector\Provider\Event\PushEvent;

/**
 * @author David Badura <d.a.badura@gmail.com>
 * @author Simon Mönch <simonmoench@gmail.com>
 */
class GitlabProviderAdapter implements ProviderAdapterInterface
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
     * @param Client    $client
     * @param Generator $generator
     */
    public function __construct(Client $client, Generator $generator)
    {
        $this->client    = $client;
        $this->generator = $generator;
    }

    /**
     * @param string $data
     *
     * @return MergeRequestEvent|PushEvent
     *
     * @throws \Exception
     */
    public function handleEventData($data)
    {
        $event = json_decode($data, true);

        if (! is_array($event)) {
            throw new \Exception('missing data');
        }

        $isMerge = \igorw\get_in($event, ['object_kind']) === 'merge_request';

        if ($isMerge) {
            return new MergeRequestEvent(
                $event['object_attributes']['source_project_id'],
                $event['object_attributes']['id'],
                $event['object_attributes']['source_branch']
            );
        }

        return new PushEvent(
            $this->normalizeBranchName($event['ref']),
            $event['project_id'],
            $event['after']
        );
    }

    /**
     * @param MergeRequest $mergeRequest
     *
     * @return string
     */
    public function addMergeRequestComment(MergeRequest $mergeRequest)
    {
        /** @var MergeRequests $mergeRequestApi */
        $mergeRequestApi = $this->client->api('merge_requests');

        return $mergeRequestApi->addComment(
            $mergeRequest->getProject()->getRemoteId(),
            $mergeRequest->getRemoteId(),
            $this->generator->getMarkdownForMergeRequest($mergeRequest)
        );
    }

    /**
     * @param MergeRequest $mergeRequest
     *
     * @throws \Exception
     */
    public function updateMergeRequest(MergeRequest $mergeRequest)
    {
        $data = $this->client->api('mr')->show(
            $mergeRequest->getProject()->getRemoteId(),
            $mergeRequest->getRemoteId()
        );

        $mergeRequest->setName($data['title']);
        $mergeRequest->setStatus($this->getMergeRequestStatus($data['state']));
    }

    /**
     * @param string $projectId
     * @param string $branch
     *
     * @return string
     */
    public function getLastRevisionFromBranch($projectId, $branch)
    {
        $result = $this->client->api('repositories')->branch($projectId, $branch);

        return $result['commit']['id'];
    }

    /**
     * @param string $remoteProjectId
     *
     * @return Project
     */
    public function createProject($remoteProjectId)
    {
        $data = $this->client->api('projects')->show($remoteProjectId);

        $project = new Project();
        $project->setRemoteId($remoteProjectId);
        $project->setName($data['name_with_namespace']);
        $project->setRepositoryUrl($data['ssh_url_to_repo']);
        $project->setWebUrl($data['web_url']);

        return $project;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gitlab';
    }

    /**
     * @param $status
     *
     * @return mixed
     */
    private function getMergeRequestStatus($status)
    {
        $statuses = [
            'merged' => MergeRequest::STATUS_MERGED,
            'opened' => MergeRequest::STATUS_OPEN,
            'closed' => MergeRequest::STATUS_CLOSED
        ];

        if (! isset($statuses[$status])) {
            throw new \RuntimeException('Merge request status is not defined');
        }

        return $statuses[$status];
    }

    /**
     * @param string $ref
     *
     * @return string
     */
    private function normalizeBranchName($ref)
    {
        /* @todo: only remove it from start of string? */
        return str_replace('refs/heads/', '', $ref);
    }
}