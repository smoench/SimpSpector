<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use SimpleThings\AppBundle\Entity\Project;

/**
 *
 * @author Simon Mönch <simonmoench@gmail.com>
 */
class ProjectRepository extends EntityRepository
{
    /**
     * @return array|Project[]
     */
    public function findAll()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $id
     *
     * @return Project|null
     */
    public function findByRemoteId($id)
    {
        return $this->createQueryBuilder('p')
            ->where('p.remoteId = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}