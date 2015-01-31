<?php

namespace SimpleThings\AppBundle\Event;

use SimpleThings\AppBundle\Logger\AbstractLogger;
use SimpleThings\AppBundle\Workspace;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class GadgetEvent extends Event
{
    /**
     * @var Workspace
     */
    protected $workspace;

    /**
     * @var AbstractLogger
     */
    private $logger;

    /**
     * @param Workspace $workspace
     * @param AbstractLogger $logger
     */
    public function __construct(Workspace $workspace, AbstractLogger $logger)
    {
        $this->workspace = $workspace;
        $this->logger    = $logger;
    }

    /**
     * @return Workspace
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     * @return AbstractLogger
     */
    public function getLogger()
    {
        return $this->logger;
    }
}