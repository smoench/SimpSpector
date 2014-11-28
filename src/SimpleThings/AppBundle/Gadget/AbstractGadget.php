<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Gadget;

use SimpleThings\AppBundle\Workspace;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        return array_key_exists($this->getName(), $workspace->config);
    }

    /**
     * @param Workspace $workspace
     * @return mixed
     */
    public function run(Workspace $workspace)
    {
        return true;
    }

    /**
     * @param Workspace $workspace
     */
    public function prepare(Workspace $workspace)
    {
    }

    /**
     * @param Workspace $workspace
     */
    public function cleanup(Workspace $workspace)
    {
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return 0;
    }
}