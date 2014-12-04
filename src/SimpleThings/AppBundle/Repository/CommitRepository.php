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
     * @param Project $project
     * @return Commit
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findLastSuccessInMaster(Project $project)
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.mergeRequest IS NULL')
            ->andWhere('c.project = :project')
            ->andWhere('c.status = :status')
            ->orderBy('c.id', 'DESC')
            ->getQuery();

        $query->setParameters(
            [
                'project' => $project,
                'status'  => Commit::STATUS_SUCCESS
            ]
        );

        $query->setMaxResults(1);

        return $query->getOneOrNullResult();
    }

    /**
     * @param Project $project
     * @param int|null $limit
     * @return Commit[]
     */
    public function findByMaster(Project $project, $limit = null)
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.mergeRequest IS NULL')
            ->andWhere('c.project = :project')
            ->orderBy('c.id', 'DESC')
            ->getQuery();

        $query->setParameters(['project' => $project]);

        if ($limit) {
            $query->setMaxResults($limit);
        }

        return $query->getResult();
    }

    /**
     * @param int|null $limit
     * @return Commit[]
     */
    public function findGlobalCommits($limit = null)
    {
        $query = $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery();

        if ($limit) {
            $query->setMaxResults($limit);
        }

        return $query->getResult();
    }

    /**
     * @param Project $project
     * @param int|null $limit
     * @return Commit[]
     */
    public function findCommitsByProject(Project $project, $limit = null)
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.project = :project')
            ->orderBy('c.createdAt', 'DESC')
            ->setParameter('project', $project)
            ->getQuery();

        if ($limit) {
            $query->setMaxResults($limit);
        }

        return $query->getResult();
    }
}
