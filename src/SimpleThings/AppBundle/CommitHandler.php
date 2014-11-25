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
     * @var GitCheckout
     */
    private $gitCheckout;

    /**
     * @param EntityManager $em
     * @param GitCheckout $gitCheckout
     */
    public function __construct(EntityManager $em, GitCheckout $gitCheckout)
    {
        $this->em = $em;
        $this->gitCheckout = $gitCheckout;
    }

    /**
     * @param Commit $commit
     */
    public function handle(Commit $commit)
    {
        $workingCopy = $this->gitCheckout->create($commit);

        // composer install
        // scrutinizer

        $this->gitCheckout->remove($workingCopy);

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