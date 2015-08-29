<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Tag;
use Doctrine\ORM\EntityRepository;

class TagRepository extends EntityRepository
{
    /**
     * @param int $remoteId
     * @param string $tagName
     * @return Tag|null
     */
    public function findTagByRemoteId($remoteId, $tagName)
    {
        $query = $this
            ->createQueryBuilder('b')
            ->join('b.project', 'p')
            ->where('p.remoteId = :projectId')
            ->andWhere('b.name = :name')
            ->getQuery();

        $query->setParameters([
            'projectId' => $remoteId,
            'name' => $tagName
        ]);

        $query->setMaxResults(1);

        return $query->getOneOrNullResult();
    }
}
