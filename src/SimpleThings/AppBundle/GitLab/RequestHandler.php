<?php

namespace SimpleThings\AppBundle\GitLab\Handler;
use SimpleThings\AppBundle\GitLab\CommitFactory;
use SimpleThings\AppBundle\GitLab\Notifier;
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
     * @param CommitFactory $commitFactory
     * @param Notifier $notifier
     */
    function __construct(CommitFactory $commitFactory, Notifier $notifier)
    {
        $this->commitFactory = $commitFactory;
        $this->notifier = $notifier;
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

        // todo...
    }
}

