<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Gadget;

use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Logger\AbstractLogger;
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
     * @param Workspace      $workspace
     * @param AbstractLogger $logger
     * @return Result
     */
    public function run(Workspace $workspace, AbstractLogger $logger);

    /**
     * @return string
     */
    public function getName();
}