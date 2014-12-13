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
     * @return Result
     */
    public function run(Workspace $workspace);

    /**
     * @return string
     */
    public function getName();
}