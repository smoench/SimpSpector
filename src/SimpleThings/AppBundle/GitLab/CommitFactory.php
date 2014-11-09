<?php

namespace SimpleThings\AppBundle\GitLab;

use Doctrine\ORM\EntityManager;
use Gitlab\Client;
use SimpleThings\AppBundle\Entity\MergeRequest;
use SimpleThings\AppBundle\Entity\Commit;
use SimpleThings\AppBundle\Entity\Project;

/**
 *
 * @author David Badura <d.a.badura@gmail.com>
 */
class CommitFactory
{
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
     */
    function __construct(EntityManager $em, Client $client)
    {
        $this->em = $em;
        $this->client = $client;
    }

    /**
     * @param array $event
     * @return Commit
     */
    public function createByPush(array $event)
    {
        $branch = str_replace('refs/heads/', '', $event['ref']);
        $projectId = $event['project_id'];
        $revision = $event['after'];

        if (!$mr = $this->findMergeRequestByBranch($projectId, $branch)) {
            return null;
        }

        $commit = new Commit();
        $commit->setMergeRequest($mr);
        $commit->setRevision($revision);

        return $commit;
    }

    /**
     * @param array $event
     * @return Commit
     */
    public function createByMergeRequest(array $event)
    {
        $projectId = $event['object_attributes']['source_project_id'];
        $mergeId = $event['object_attributes']['id'];
        $branch = $event['object_attributes']['source_branch'];

        $mr = $this->createMergeRequest($projectId, $mergeId, $branch);
        $revision = $this->getLastRevisionFromBranch($projectId, $branch);

        $commit = new Commit();
        $commit->setMergeRequest($mr);
        $commit->setRevision($revision);

        return $commit;
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
}