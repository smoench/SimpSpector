<?php
/**
 *
 */

namespace SimpleThings\AppBundle;

use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Gadget\Repository;

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
     * @param Workspace $workspace
     * @return Issue[]
     */
    public function run(Workspace $workspace)
    {
        $gadgets = $this->repository->getSortedGadgets();
        $issues = [];

        foreach ($gadgets as $gadget) {
            if (!$gadget->isActive($workspace)) {
                continue;
            }

            $issues = array_merge($issues, $gadget->run($workspace));
        }

        return $issues;
    }
} 
