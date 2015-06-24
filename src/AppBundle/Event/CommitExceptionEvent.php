<?php

namespace AppBundle\Event;

use SimpleThings\AppBundle\Entity\Commit;
use SimpSpector\Analyser\Logger\AbstractLogger;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class CommitExceptionEvent extends CommitEvent
{
    /**
     * @var \Exception
     */
    private $exception;

    /**
     * @param Commit $commit
     * @param AbstractLogger $logger
     * @param \Exception $exception
     */
    public function __construct(Commit $commit, AbstractLogger $logger, \Exception $exception)
    {
        parent::__construct($commit, $logger);
        $this->exception = $exception;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}
