<?php

namespace SimpleThings\AppBundle\GitLab;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Gitlab\Client;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SimpleThings\AppBundle\Entity\Commit;
use SimpleThings\AppBundle\Entity\MergeRequest;
use SimpleThings\AppBundle\Entity\Project;
use SimpleThings\AppBundle\Repository\MergeRequestRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author David Badura <d.a.badura@gmail.com>
 */
class RequestHandler
{
    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Client
     */
    private $client;

    /**
     * @param EntityManager $em
     * @param Client $client
     * @param Notifier $notifier
     * @param LoggerInterface $logger
     */
    public function __construct(
        EntityManager $em,
        Client $client,
        Notifier $notifier,
        LoggerInterface $logger = null
    ) {
        $this->notifier = $notifier;
        $this->em       = $em;
        $this->client   = $client;
        $this->logger   = $logger ?: new NullLogger();
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function handle(Request $request)
    {
        $this->logger->info('new request', ['data' => $request->getContent()]);

        $event = json_decode($request->getContent(), true);

        if (!is_array($event)) {
            throw new \Exception('missing data');
        }

        if (\igorw\get_in($event, ['object_kind']) === 'merge_request') {
            $this->handleMergeEvent($event);
        } else {
            $this->handlePushEvent($event);
        }
    }

    /**
     * @param array $event
     */
    private function handleMergeEvent(array $event)
    {
        $projectId = $event['object_attributes']['source_project_id'];
        $mergeId   = $event['object_attributes']['id'];
        $branch    = $event['object_attributes']['source_branch'];

        /** @var MergeRequestRepository $repository */
        $repository = $this->em->getRepository('SimpleThingsAppBundle:MergeRequest');

        if ($mergeRequest = $repository->findMergeRequestByRemote($projectId, $mergeId)) {
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
     * @param array $event
     */
    private function handlePushEvent(array $event)
    {
        $branch    = $this->normalizeBranchName($event['ref']);
        $projectId = $event['project_id'];
        $revision  = $event['after'];

        /** @var MergeRequestRepository $repository */
        $repository = $this->em->getRepository('SimpleThingsAppBundle:MergeRequest');

        if(!$project = $this->findProject($projectId)) {
            $project = $this->createProject($projectId);
        }

        if ($branch == 'master') {
            $mergeRequest = null;
        } elseif (!$mergeRequest = $repository->findLastMergeRequestByBranch($project, $branch)) {
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
     * @return string
     */
    private function getLastRevisionFromBranch($projectId, $branch)
    {
        $result = $this->client->api('repositories')->branch($projectId, $branch);

        return $result['commit']['id'];
    }

    /**
     * @param $projectId
     * @param $mergeRequestId
     * @param $branch
     * @return MergeRequest
     */
    private function createMergeRequest($projectId, $mergeRequestId, $branch)
    {
        $mr = new MergeRequest();
        $mr->setRemoteId($mergeRequestId);
        $mr->setSourceBranch($branch);

        if (!$project = $this->findProject($projectId)) {
            $project = $this->createProject($projectId);
        }

        $mr->setProject($project);

        return $mr;
    }

    /**
     * @param MergeRequest $mergeRequest
     * @throws \Exception
     */
    private function updateMergeRequest(MergeRequest $mergeRequest)
    {
        $data = $this->client->api('mr')->show(
            $mergeRequest->getProject()->getRemoteId(),
            $mergeRequest->getRemoteId()
        );

        $mergeRequest->setName($data['title']);

        switch ($data['state']) {
            case "merged":
                $mergeRequest->setStatus(MergeRequest::STATUS_MERGED);
                break;
            case "opened":
                $mergeRequest->setStatus(MergeRequest::STATUS_OPEN);
                break;
            case "closed":
                $mergeRequest->setStatus(MergeRequest::STATUS_CLOSED);
                break;
            default:
                throw new \Exception();
        }
    }

    /**
     * @param string $projectId
     * @return null|object
     */
    private function findProject($projectId)
    {
        /** @var EntityRepository $projectRepository */
        $projectRepository = $this->em->getRepository('SimpleThings\AppBundle\Entity\Project');
        $results           = $projectRepository->findBy(['remoteId' => $projectId]);

        return count($results) === 1 ? $results[0] : null;
    }

    /**
     * @param string $projectId
     * @return Project
     */
    private function createProject($projectId)
    {
        $data = $this->client->api('projects')->show($projectId);

        $project = new Project();
        $project->setRemoteId($projectId);
        $project->setName($data['name_with_namespace']);
        $project->setRepositoryUrl($data['ssh_url_to_repo']);
        $project->setWebUrl($data['web_url']);

        return $project;
    }

    /**
     * @param string $ref
     * @return string
     */
    private function normalizeBranchName($ref)
    {
        /* @todo: only remove it from start of string? */
        return str_replace('refs/heads/', '', $ref);
    }
}
