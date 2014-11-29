<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use SimpleThings\AppBundle\Entity\Commit;
use SimpleThings\AppBundle\Entity\MergeRequest;
use SimpleThings\AppBundle\Entity\Project;

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
     * @param Project $project
     * @return Commit
     */
    public function findLastInMaster(Project $project)
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.mergeRequest IS NULL')
            ->andWhere('c.project = :project')
            ->orderBy('c.id', 'DESC')
            ->getQuery();

        $query->setParameters(['project' => $project]);

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
