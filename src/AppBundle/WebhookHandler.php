<?php

namespace AppBundle;

use AppBundle\Badge\MarkdownGeneratorInterface;
use AppBundle\Entity\Project;
use AppBundle\Provider\ProviderInterface;
use DavidBadura\GitWebhooks\Event\AbstractEvent;
use DavidBadura\GitWebhooks\Event\MergeRequestEvent;
use DavidBadura\GitWebhooks\Event\PushEvent;
use Psr\Log\NullLogger;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class WebhookHandler
{
    /**
     * @var ProviderInterface
     */
    private $provider;

    /**
     * @var MarkdownGeneratorInterface
     */
    private $markdownGenerator;

    /**
     * @var MergeRequestRepository
     */
    private $mergeRequestRepository;

    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EntityManager          $em
     * @param MergeRequestRepository $mergeRequestRepository
     * @param ProjectRepository      $projectRepository
     * @param Client                 $client
     * @param Notifier               $notifier
     * @param LoggerInterface        $logger
     */
    public function __construct(
        EntityManager $em,
        MergeRequestRepository $mergeRequestRepository,
        ProjectRepository $projectRepository,
        Client $client,
        Notifier $notifier,
        LoggerInterface $logger = null
    ) {
        $this->notifier               = $notifier;
        $this->em                     = $em;
        $this->mergeRequestRepository = $mergeRequestRepository;
        $this->projectRepository      = $projectRepository;
        $this->client                 = $client;
        $this->logger                 = $logger ?: new NullLogger();
    }

    /**
     * @param AbstractEvent $event
     *
     * @throws \Exception
     */
    public function handle(AbstractEvent $event)
    {
        if ($event instanceof MergeRequestEvent) {
            $this->handleMergeEvent($event);
        } elseif ($event instanceof PushEvent) {
            $this->handlePushEvent($event);
        }
    }

    /**
     * @param MergeRequestEvent $event
     */
    private function handleMergeEvent(MergeRequestEvent $event)
    {
        $mergeRequest = $this->mergeRequestRepository->findMergeRequestByRemote($event->repository->id, $event->id);

        if ($mergeRequest) {
            $this->updateMergeRequest($mergeRequest);

            return;
        }

        $mergeRequest = $this->createMergeRequest($projectId, $mergeId, $branch);
        $this->updateMergeRequest($mergeRequest);

        $revision = $this->getLastRevisionFromBranch($projectId, $branch);

        $commit = new Commit();
        $commit->setProject($mergeRequest->getProject());
        $commit->setMergeRequest($mergeRequest);
        $commit->setRevision($revision);

        $this->em->persist($commit);
        $this->em->flush();

        $this->notifier->notify($commit->getMergeRequest());
    }

    /**
     * @param PushEvent $event
     */
    private function handlePushEvent(PushEvent $event)
    {
        $branch    = $this->normalizeBranchName($event['ref']);
        $projectId = $event['project_id'];
        $revision  = $event['after'];

        if (! $project = $this->projectRepository->findByRemoteId($projectId)) {
            $project = $this->createProject($projectId);
        }

        if ($branch == 'master') {
            $mergeRequest = null;
        } elseif (! $mergeRequest = $this->mergeRequestRepository->findLastMergeRequestByBranch($project, $branch)) {
            return;
        }

        $commit = new Commit();
        $commit->setProject($project);
        $commit->setMergeRequest($mergeRequest);
        $commit->setRevision($revision);

        $this->em->persist($commit);
        $this->em->flush();
    }


    /**
     * @param string $projectId
     * @param string $branch
     *
     * @return string
     */
    private function getLastRevisionFromBranch($projectId, $branch)
    {
        $result = $this->client->api('repositories')->branch($projectId, $branch);

        return $result['commit']['id'];
    }

    /**
     * @param $remoteProjectId
     * @param $mergeRequestId
     * @param $branch
     *
     * @return MergeRequest
     */
    private function createMergeRequest($remoteProjectId, $mergeRequestId, $branch)
    {
        $mr = new MergeRequest();
        $mr->setRemoteId($mergeRequestId);
        $mr->setSourceBranch($branch);

        if (! $project = $this->projectRepository->findByRemoteId($remoteProjectId)) {
            $project = $this->createProject($remoteProjectId);
        }

        $mr->setProject($project);

        return $mr;
    }

    /**
     * @param MergeRequest $mergeRequest
     *
     * @throws \Exception
     */
    private function updateMergeRequest(MergeRequest $mergeRequest)
    {
        $data = $this->client->api('mr')->show(
            $mergeRequest->getProject()->getRemoteId(),
            $mergeRequest->getRemoteId()
        );

        $mergeRequest->setName($data['title']);
        $mergeRequest->setStatus($this->getMergeRequestStatus($data['state']));
    }

    /**
     * @param int $remoteProjectId
     *
     * @return Project
     */
    private function createProject($remoteProjectId)
    {
        $projectInfo = $this->provider->getProjectInformation($remoteProjectId);

        $project = new Project();
        $project->setRemoteId($remoteProjectId);
        $project->setName($projectInfo->namespace . '/' . $projectInfo->name);
        $project->setRepositoryUrl($projectInfo->repositoryUrl);
        $project->setWebUrl($projectInfo->webUrl);

        return $project;
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
        return str_replace('refs/heads/', '', $ref);
    }
}