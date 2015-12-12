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
class NewsStreamItemRepository extends EntityRepository
{
    public function findByProject(Project $project)
    {
        return $this->createQueryBuilder("n")
            ->where('n.project = :project')
            ->orderBy('n.createdAt', 'DESC')
            ->setParameter('project', $project)
            ->getQuery()
            ->getResult();

    }
}
