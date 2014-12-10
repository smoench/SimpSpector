<?php
/**
 *
 */

namespace SimpleThings\AppBundle;

use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Gadget\Repository;
use SimpleThings\AppBundle\Logger\AbstractLogger;
use SimpleThings\AppBundle\Logger\NullLogger;
use SimpleThings\AppBundle\Gadget\Result;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class GadgetExecutor
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Workspace      $workspace
     * @param AbstractLogger $logger
     * @return Result
     */
    public function run(Workspace $workspace, AbstractLogger $logger = null)
    {
        $logger = $logger ?: new NullLogger();

        $gadgets = $this->repository->getGadgets();
        $result  = new Result();

        foreach ($gadgets as $gadget) {
            if (!$gadget->isActive($workspace)) {
                continue;
            }

            $result->merge($gadget->run($workspace, $logger));
        }

        return $result;
    }
} 
