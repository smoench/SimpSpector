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
class BranchRepository extends EntityRepository
{
    /**
     * @param int $remoteId
     * @param string $branchName
     * @return MergeRequest|null
     */
    public function findBranchByRemoteId($remoteId, $branchName)
    {
        $query = $this->createQueryBuilder('b')
            ->join('b.project', 'p')
            ->where('p.remoteId = :projectId')
            ->andWhere('b.name = :name')
            ->getQuery();

        $query->setParameters([
            'projectId' => $remoteId,
            'name'      => $branchName
        ]);

        $query->setMaxResults(1);

        return $query->getOneOrNullResult();
    }
}
