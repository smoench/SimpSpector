<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use SimpleThings\AppBundle\Entity\MergeRequest;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class MergeRequestRepository extends EntityRepository
{
    /**
     * @param int $projectId
     * @param int $mergeId
     * @return bool
     */
    public function hasMergeRequest($projectId, $mergeId)
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

        return count($query->getResult()) > 0 ? true : false;
    }

    /**
     * @param string $projectId
     * @param string $branch
     * @return MergeRequest
     */
    public function findLastMergeRequestByBranch($projectId, $branch)
    {
        $query = $this->createQueryBuilder('m')
            ->join('m.project', 'p')
            ->where('p.remoteId = :project')
            ->andWhere('m.sourceBranch = :branch')
            ->orderBy('m.id', 'DESC')
            ->getQuery();

        $query->setParameters([
            'project' => $projectId,
            'branch'  => $branch
        ]);

        return $query->getOneOrNullResult();
    }
} 