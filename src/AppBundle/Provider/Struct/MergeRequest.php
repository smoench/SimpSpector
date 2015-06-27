<?php

namespace AppBundle\Provider\Struct;

use DavidBadura\GitWebhooks\Event\MergeRequestEvent;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class MergeRequest
{
    const STATUS_OPENED = MergeRequestEvent::STATE_OPENED;
    const STATUS_MERGED = MergeRequestEvent::STATE_MERGED;
    const STATUS_CLOSED = MergeRequestEvent::STATE_CLOSED;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $status;
}