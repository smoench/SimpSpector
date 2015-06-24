<?php

namespace AppBundle\Event;

use AppBundle\Entity\Commit;
use SimpSpector\Analyser\Logger\AbstractLogger;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class CommitEvent extends Event
{
    /**
     * @var Commit
     */
    private $commit;

    /**
     * @var AbstractLogger
     */
    private $logger;

    /**
     * @param Commit $commit
     * @param AbstractLogger $logger
     */
    public function __construct(Commit $commit, AbstractLogger $logger)
    {
        $this->commit = $commit;
        $this->logger = $logger;
    }

    /**
     * @return Commit
     */
    public function getCommit()
    {
        return $this->commit;
    }

    /**
     * @return AbstractLogger
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
