<?php
/**
 *
 */

namespace SimpleThings\AppBundle;

use Doctrine\ORM\EntityManager;
use SimpleThings\AppBundle\Entity\Commit;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class JobManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return Commit[]
     */
    public function getNewCommits()
    {
        return $this->em->getRepository('SimpleThingsAppBundle:Commit')->findBy(['status' => Commit::STATUS_NEW]);
    }

    /**
     * @param Commit $commit
     */
    public function add(Commit $commit)
    {
        $this->em->persist($commit);
    }

    /**
     *
     */
    public function flush()
    {
        $this->em->flush();
    }
} 