<?php

namespace SimpleThings\AppBundle\Event;

use SimpleThings\AppBundle\Gadget\Result;
use SimpleThings\AppBundle\Logger\AbstractLogger;
use SimpleThings\AppBundle\Workspace;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class GadgetResultEvent extends GadgetEvent
{
    /**
     * @var Result
     */
    protected $result;

    /**
     * @param Workspace $workspace
     * @param AbstractLogger $logger
     * @param Result $result
     */
    public function __construct(Workspace $workspace, AbstractLogger $logger, Result $result)
    {
        parent::__construct($workspace, $logger);

        $this->result = $result;
    }

    /**
     * @return Result
     */
    public function getResult()
    {
        return $this->result;
    }
}