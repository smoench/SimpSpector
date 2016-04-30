<?php

namespace AppBundle\Event\Subscriber;

use AppBundle\Badge\MarkdownGenerator;
use AppBundle\Event\MergeRequestEvent;
use AppBundle\Events;
use AppBundle\Provider\ProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class BadgeSubscriber implements EventSubscriberInterface
{
    /**
     * @var ProviderInterface
     */
    private $provider;

    /**
     * @var MarkdownGenerator
     */
    private $badgeGenerator;

    /**
     * @param ProviderInterface $provider
     * @param MarkdownGenerator $badgeGenerator
     */
    public function __construct(ProviderInterface $provider, MarkdownGenerator $badgeGenerator)
    {
        $this->provider = $provider;
        $this->badgeGenerator = $badgeGenerator;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::NEW_MERGE_REQUEST => array('onNewMergeRequest', 0)
        ];
    }

    /**
     * @param MergeRequestEvent $event
     */
    public function onNewMergeRequest(MergeRequestEvent $event)
    {
        $mergeRequest = $event->getMergeRequest();

        $comment = $this->badgeGenerator->generateForMergeRequest($mergeRequest);

        $this->provider->addMergeRequestComment(
            $mergeRequest,
            $comment
        );
    }
}
