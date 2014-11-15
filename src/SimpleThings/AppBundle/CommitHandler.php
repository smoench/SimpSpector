<?php

namespace SimpleThings\AppBundle;

use Doctrine\ORM\EntityManager;
use SimpleThings\AppBundle\Entity\Commit;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class CommitHandler
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Commit $commit
     */
    public function handle(Commit $commit)
    {
        $this->save($commit);
    }

    /**
     * @param Commit $commit
     */
    private function save(Commit $commit)
    {
        $this->em->persist($commit);
        $this->em->flush();
    }
} 