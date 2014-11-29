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
abstract class AbstractGadget implements GadgetInterface
{
    /**
     * @param Workspace $workspace
     * @return bool
     */
    public function isActive(Workspace $workspace)
    {
        return array_key_exists($this->getName(), $workspace->config)
        && $workspace->config[$this->getName()] !== false;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return 0;
    }
}