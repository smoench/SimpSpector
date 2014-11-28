<?php
/**
 *
 */

namespace SimpleThings\AppBundle;

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
    function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Workspace $workspace
     * @return array
     */
    public function run(Workspace $workspace)
    {
        $gadgets = $this->repository->getSortedGadgets();

        foreach ($gadgets as $gadget) {
            if (!$gadget->isActive($workspace)) {
                continue;
            }

            $gadget->prepare($workspace);
        }

        $result = [];
        foreach ($gadgets as $gadget) {
            if (!$gadget->isActive($workspace)) {
                continue;
            }

            $result[$gadget->getName()] = $gadget->run($workspace);
        }

        foreach ($gadgets as $gadget) {
            if (!$gadget->isActive($workspace)) {
                continue;
            }

            $gadget->cleanup($workspace);
        }

        return $result;
    }
} 