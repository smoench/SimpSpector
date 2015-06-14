<?php

namespace SimpSpector\Analyser\Event\Subscriber;

use SimpleThings\AppBundle\Event\CommitEvent;
use SimpleThings\AppBundle\Event\CommitExceptionEvent;
use SimpleThings\AppBundle\Event\CommitResultEvent;
use SimpleThings\AppBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::BEGIN     => array('onBegin', 0),
            Events::RESULT    => array('onResult', 0),
            Events::EXCEPTION => array('onException', 0)
        ];
    }

    /**
     * @param CommitEvent $event
     */
    public function onBegin(CommitEvent $event)
    {
        $logger = $event->getLogger();

        $logger->writeln();
        $logger->writeln("Go go gadgets!");
        $logger->writeln();
    }

    /**
     * @param CommitResultEvent $event
     */
    public function onResult(CommitResultEvent $event)
    {
        $logger = $event->getLogger();

        $logger->writeln("");
        $logger->writeln("finish :)");
    }

    /**
     * @param CommitExceptionEvent $event
     */
    public function onException(CommitExceptionEvent $event)
    {
        $logger = $event->getLogger();
        $e      = $event->getException();

        $logger->writeln();
        $logger->writeln(">> EXCEPTION <<");
        $logger->writeln();

        $logger->writeln($e->getMessage());
        $logger->writeln($e->getTraceAsString());

    }
}
