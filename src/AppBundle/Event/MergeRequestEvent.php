<?php

namespace AppBundle\Event;

use AppBundle\Entity\MergeRequest;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class MergeRequestEvent extends Event
{
    /**
     * @var MergeRequest
     */
    private $mergeRequest;

    /**
     * @param MergeRequest $mergeRequest
     */
    public function __construct(MergeRequest $mergeRequest)
    {
        $this->mergeRequest = $mergeRequest;
    }

    /**
     * @return MergeRequest
     */
    public function getMergeRequest()
    {
        return $this->mergeRequest;
    }
}
