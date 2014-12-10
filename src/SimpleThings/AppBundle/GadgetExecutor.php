<?php
/**
 *
 */

namespace SimpleThings\AppBundle;

use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Gadget\Repository;
use SimpleThings\AppBundle\Logger\AbstractLogger;
use SimpleThings\AppBundle\Logger\NullLogger;

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
     * @return Issue[]
     */
    public function run(Workspace $workspace, AbstractLogger $logger = null)
    {
        $logger = $logger ?: new NullLogger();

        $gadgets = $this->repository->getSortedGadgets();
        $issues = [];

        foreach ($gadgets as $gadget) {
            if (!$gadget->isActive($workspace)) {
                continue;
            }

            $issues = array_merge($issues, $gadget->run($workspace, $logger));
        }

        return $issues;
    }
} 
