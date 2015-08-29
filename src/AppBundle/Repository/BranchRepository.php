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
     * @param int $projectId
     * @param string $branchName
     * @return MergeRequest|null
     */
    public function findBranchByRemote($projectId, $branchName)
    {
        $query = $this->createQueryBuilder('b')
            ->join('b.project', 'p')
            ->where('p.remoteId = :projectId')
            ->andWhere('b.name = :name')
            ->getQuery();

        $query->setParameters([
            'projectId' => $projectId,
            'name'      => $branchName
        ]);

        $query->setMaxResults(1);

        return $query->getOneOrNullResult();
    }
}
