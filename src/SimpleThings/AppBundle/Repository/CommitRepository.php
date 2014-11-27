<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use SimpleThings\AppBundle\Entity\Commit;

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
} 