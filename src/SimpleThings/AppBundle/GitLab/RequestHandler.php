<?php

namespace SimpleThings\AppBundle\GitLab;

use SimpleThings\AppBundle\CommitHandler;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author David Badura <d.a.badura@gmail.com>
 */
class RequestHandler
{
    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @var CommitFactory
     */
    private $commitFactory;

    /**
     * @var CommitHandler
     */
    private $commitHandler;

    /**
     * @param CommitFactory $commitFactory
     * @param Notifier $notifier
     */
    function __construct(CommitFactory $commitFactory, Notifier $notifier, CommitHandler $commitHandler)
    {
        $this->commitFactory = $commitFactory;
        $this->notifier = $notifier;
        $this->commitHandler = $commitHandler;
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function handle(Request $request)
    {
        $event = json_decode($request->getContent(), true);

        if (!is_array($event)) {
            throw new \Exception('missing data');
        }

        if (\igorw\get_in($event, ['object_kind']) === 'merge_request') {
            $commit = $this->commitFactory->createByMergeRequest($event);
            $this->notifier->notify($commit->getMergeRequest());
        } else {
            $commit = $this->commitFactory->createByPush($event);

            if (!$commit) {
                return;
            }
        }

        $this->commitHandler->handle($commit);
    }
}
