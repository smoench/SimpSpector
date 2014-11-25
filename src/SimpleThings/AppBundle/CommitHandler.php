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
     * @var GadgetExecutor
     */
    private $gadgetExecutor;

    /**
     * @param EntityManager $em
     * @param GitCheckout $gitCheckout
     * @param GadgetExecutor $gadgetExecutor
     */
    public function __construct(EntityManager $em, GitCheckout $gitCheckout, GadgetExecutor $gadgetExecutor)
    {
        $this->em = $em;
        $this->gitCheckout = $gitCheckout;
        $this->gadgetExecutor = $gadgetExecutor;
    }

    /**
     * @param Commit $commit
     */
    public function handle(Commit $commit)
    {
        $workspace = $this->gitCheckout->create($commit);

        $commit->setResult(
            $this->gadgetExecutor->run($workspace)
        );

        //$this->gitCheckout->remove($workspace);
    }    
} 
