<?php
/**
 *
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\MergeRequest;
use AppBundle\Entity\Project;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class MergeRequestRepository extends EntityRepository
{
    /**
     * @param int $projectId
     * @param int $mergeId
     * @return MergeRequest|null
     */
    public function findMergeRequestByRemote($projectId, $mergeId)
    {
        $query = $this->createQueryBuilder('m')
            ->join('m.project', 'p')
            ->where('p.remoteId = :projectId')
            ->andWhere('m.remoteId = :mergeId')
            ->getQuery();

        $query->setParameters([
            'projectId' => $projectId,
            'mergeId'   => $mergeId
        ]);

        $query->setMaxResults(1);

        return $query->getOneOrNullResult();
    }

    /**
     * @param Project $project
     * @param string $branch
     * @return MergeRequest
     */
    public function findLastMergeRequestByBranch(Project $project, $branch)
    {
        $query = $this->createQueryBuilder('m')
            ->where('m.project = :project')
            ->andWhere('m.sourceBranch = :branch')
            ->orderBy('m.id', 'DESC')
            ->getQuery();

        $query->setParameters([
            'project' => $project,
            'branch'  => $branch
        ]);

        $query->setMaxResults(1);

        return $query->getOneOrNullResult();
    }
}
