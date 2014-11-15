<?php

namespace SimpleThings\AppBundle\GitLab;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CommitFactory $commitFactory
     * @param Notifier $notifier
     * @param CommitHandler $commitHandler
     * @param LoggerInterface $logger
     */
    function __construct(
        CommitFactory $commitFactory,
        Notifier $notifier,
        CommitHandler $commitHandler,
        LoggerInterface $logger = null
    ) {
        $this->commitFactory = $commitFactory;
        $this->notifier = $notifier;
        $this->commitHandler = $commitHandler;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function handle(Request $request)
    {
        $this->logger->info('new request', ['data' => $request->getContent()]);

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
