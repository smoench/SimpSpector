<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use SimpleThings\AppBundle\Entity\Commit;
use SimpleThings\AppBundle\Entity\MergeRequest;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class CommitRepository extends EntityRepository
{
    /**
     * @return Commit[]
     */
    public function findNewCommits()
    {
        return $this->findBy(['status' => Commit::STATUS_NEW]);
    }

    /**
     * @param MergeRequest $mergeRequest
     * @return Commit
     */
    public function findLastForMergeRequest(MergeRequest $mergeRequest)
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.mergeRequest = :merge_request')
            ->orderBy('c.id', 'DESC')
            ->getQuery();

        $query->setParameters(['merge_request' => $mergeRequest]);
        $query->setMaxResults(1);

        return $query->getOneOrNullResult();
    }

    /**
     * @return Commit
     */
    public function findLastInMaster()
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.mergeRequest IS NULL')
            ->orderBy('c.id', 'DESC')
            ->getQuery();

        $query->setMaxResults(1);

        return $query->getOneOrNullResult();
    }

    /**
     * @param null $limit
     * @return Commit[]
     */
    public function findByMaster($limit = null)
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.mergeRequest IS NULL')
            ->orderBy('c.id', 'DESC')
            ->getQuery();

        if ($limit) {
            $query->setMaxResults($limit);
        }

        return $query->getResult();
    }
}
