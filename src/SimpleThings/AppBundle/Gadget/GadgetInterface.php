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
interface GadgetInterface
{
    /**
     * @param Workspace $workspace
     * @return bool
     */
    public function isActive(Workspace $workspace);

    /**
     * @param Workspace $workspace
     * @return mixed
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