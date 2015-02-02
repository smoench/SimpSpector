<?php

namespace SimpleThings\AppBundle\Event\Listener;

use SimpleThings\AppBundle\Event\GadgetResultEvent;
use SimpleThings\AppBundle\Util\HighlightHelper;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class SimpleHighlightListener
{
    /**
     * @param GadgetResultEvent $event
     */
    public function onGadgetResult(GadgetResultEvent $event)
    {
        $result    = $event->getResult();
        $workspace = $event->getWorkspace();

        foreach ($result->getIssues() as $issue) {
            if ($issue->getDescription()) {
                continue;
            }

            if (!$issue->getFile() || !$issue->getLine()) {
                continue;
            }

            $issue->setDescription(HighlightHelper::createCodeSnippet($workspace, $issue));
        }
    }
}