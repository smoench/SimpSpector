<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Gadget;

use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Workspace;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
interface GadgetInterface
{
    /**
     * @param Workspace $workspace
     * @return bool
     */
    public function isActive(Workspace $workspace);

    /**
     * @param Workspace $workspace
     * @return Issue[]
     */
    public function run(Workspace $workspace);

    /**
     * @param Workspace $workspace
     */
    public function prepare(Workspace $workspace);

    /**
     * @param Workspace $workspace
     */
    public function cleanup(Workspace $workspace);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @return string
     */
    public function getName();
}