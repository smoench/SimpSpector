<?php

namespace SimpleThings\AppBundle\GitLab;

use Doctrine\ORM\EntityManager;
use Gitlab\Client;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SimpleThings\AppBundle\Entity\Commit;
use SimpleThings\AppBundle\Entity\MergeRequest;
use SimpleThings\AppBundle\Entity\Project;
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
    function __construct(
        EntityManager $em,
        Client $client,
        Notifier $notifier,
        LoggerInterface $logger = null
    ) {
        $this->notifier = $notifier;
        $this->em = $em;
        $this->client = $client;
        $this->logger = $logger ?: new NullLogger();
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
        $mergeId = $event['object_attributes']['id'];
        $branch = $event['object_attributes']['source_branch'];

        if ($this->existMergeRequest($projectId, $mergeId)) {
            $this->logger->info("merge request exist already");
            return;
        }

        $mr = $this->createMergeRequest($projectId, $mergeId, $branch);
        $revision = $this->getLastRevisionFromBranch($projectId, $branch);

        $commit = new Commit();
        $commit->setMergeRequest($mr);
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
        // todo...

        $branch = str_replace('refs/heads/', '', $event['ref']);
        $projectId = $event['project_id'];
        $revision = $event['after'];

        if (!$mr = $this->findMergeRequestByBranch($projectId, $branch)) {
            return;
        }

        $commit = new Commit();
        $commit->setMergeRequest($mr);
        $commit->setRevision($revision);
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
     * @param string $projectId
     * @param string $branch
     * @return int
     */
    private function findMergeRequestByBranch($projectId, $branch)
    {
        $query = $this->em->createQuery('
            SELECT m, p
            FROM SimpleThings\AppBundle\Entity\MergeRequest m
            JOIN m.project p
            WHERE p.remoteId = :project
                AND m.sourceBranch = :branch
            ORDER BY m.id DESC
        ');

        $query->setMaxResults(1);

        return $query->getFirstResult();
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
     * @param string $projectId
     * @return null|object
     */
    private function findProject($projectId)
    {
        return $this->em->getRepository('SimpleThings\AppBundle\Entity\Project')->findOneBy(array(
            'remoteId' => $projectId
        ));
    }

    /**
     * @param string $projectId
     * @return Project
     */
    private function createProject($projectId)
    {
        $project = new Project();
        $project->setRemoteId($projectId);

        return $project;
    }

    /**
     * @param int $projectId
     * @param int $mergeId
     * @return bool
     */
    private function existMergeRequest($projectId, $mergeId)
    {
        return (bool) $this->em->createQuery('
            SELECT COUNT(m)
            FROM SimpleThings\AppBundle\Entity\MergeRequest m
            JOIN m.project p
            WHERE p.remoteId = :projectId
                AND m.remoteId = :mergeId
        ')->setParameters(
            [
                'projectId' => $projectId,
                'mergeId'   => $mergeId
            ]
        )->getSingleScalarResult();
    }
}
