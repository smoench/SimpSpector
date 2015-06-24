<?php

namespace AppBundle\Event;

use AppBundle\Entity\Commit;
use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Result;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class CommitResultEvent extends CommitEvent
{
    /**
     * @var Result
     */
    private $result;

    /**
     * @param Commit $commit
     * @param AbstractLogger $logger
     * @param Result $result
     */
    public function __construct(Commit $commit, AbstractLogger $logger, Result $result)
    {
        parent::__construct($commit, $logger);
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
