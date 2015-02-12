<?php

namespace SimpSpector\Provider;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SimpleThings\AppBundle\Entity\Commit;
use SimpleThings\AppBundle\Entity\MergeRequest;
use SimpleThings\AppBundle\Repository\MergeRequestRepository;
use SimpleThings\AppBundle\Repository\ProjectRepository;
use SimpSpector\Provider\Event\MergeRequestEvent;
use SimpSpector\Provider\Event\PushEvent;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author David Badura <d.a.badura@gmail.com>
 * @author Simon Mönch <simonmoench@gmail.com>
 */
class RequestHandler
{
    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var MergeRequestRepository
     */
    private $mergeRequestRepository;

    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ProviderAdapterInterface
     */
    private $adapter;

    /**
     * @param EntityManager            $em
     * @param MergeRequestRepository   $mergeRequestRepository
     * @param ProjectRepository        $projectRepository
     * @param ProviderAdapterInterface $adapter
     * @param Notifier                 $notifier
     * @param LoggerInterface          $logger
     */
    public function __construct(
        EntityManager $em,
        MergeRequestRepository $mergeRequestRepository,
        ProjectRepository $projectRepository,
        ProviderAdapterInterface $adapter,
        Notifier $notifier,
        LoggerInterface $logger = null
    ) {
        $this->notifier               = $notifier;
        $this->em                     = $em;
        $this->mergeRequestRepository = $mergeRequestRepository;
        $this->projectRepository      = $projectRepository;
        $this->adapter                = $adapter;
        $this->logger                 = $logger ?: new NullLogger();
    }

    /**
     * @param Request $request
     *
     * @throws \Exception
     */
    public function handle(Request $request)
    {
        $this->logger->info('new request', ['data' => $request->getContent()]);

        $event = $this->adapter->handleEventData($request->getContent());

        if ($event instanceof MergeRequestEvent) {
            $this->handleMergeEvent($event);
        } elseif ($event instanceof PushEvent) {
            $this->handlePushEvent($event);
        }
    }

    /**
     * @param MergeRequestEvent $event
     */
    private function handleMergeEvent(MergeRequestEvent $event)
    {
        $projectId = $event->getProjectId();
        $mergeId   = $event->getMergeId();
        $branch    = $event->getBranch();

        $mergeRequest = $this->mergeRequestRepository->findMergeRequestByRemote($projectId, $mergeId);

        if ($mergeRequest) {
            $this->adapter->updateMergeRequest($mergeRequest);

            return;
        }

        $mergeRequest = $this->createMergeRequest($projectId, $mergeId, $branch);

        $this->adapter->updateMergeRequest($mergeRequest);

        $revision = $this->adapter->getLastRevisionFromBranch($projectId, $branch);

        $commit = new Commit();
        $commit->setProject($mergeRequest->getProject());
        $commit->setMergeRequest($mergeRequest);
        $commit->setRevision($revision);

        $this->em->persist($commit);
        $this->em->flush();

        $this->notifier->notify($commit->getMergeRequest());
    }

    /**
     * @param PushEvent $event
     */
    private function handlePushEvent(PushEvent $event)
    {
        $branch    = $event->getBranch();
        $projectId = $event->getProjectId();
        $revision  = $event->getRevision();

        if (! $project = $this->projectRepository->findByRemoteId($projectId)) {
            $project = $this->adapter->createProject($projectId);
        }

        if ($branch == 'master') {
            $mergeRequest = null;
        } elseif (! $mergeRequest = $this->mergeRequestRepository->findLastMergeRequestByBranch($project, $branch)) {
            return;
        }

        $commit = new Commit();
        $commit->setProject($project);
        $commit->setMergeRequest($mergeRequest);
        $commit->setRevision($revision);

        $this->em->persist($commit);
        $this->em->flush();
    }

    /**
     * @param $remoteProjectId
     * @param $mergeRequestId
     * @param $branch
     *
     * @return MergeRequest
     */
    private function createMergeRequest($remoteProjectId, $mergeRequestId, $branch)
    {
        $mr = new MergeRequest();
        $mr->setRemoteId($mergeRequestId);
        $mr->setSourceBranch($branch);

        if (! $project = $this->projectRepository->findByRemoteId($remoteProjectId)) {
            $project = $this->adapter->createProject($remoteProjectId);
        }

        $mr->setProject($project);

        return $mr;
    }
}
