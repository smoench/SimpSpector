<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Gadget;

use SimpleThings\AppBundle\Workspace;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
interface GadgetInterface
{
    /**
     * @param Workspace $workspace
     * @return mixed
     */
    public function run(Workspace $workspace);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @return string
     */
    public function getName();
}