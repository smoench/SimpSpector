<?php

namespace AppBundle;

use AppBundle\Entity\Branch;
use AppBundle\Entity\Commit;
use AppBundle\Entity\MergeRequest;
use AppBundle\Entity\Project;
use DavidBadura\GitWebhooks\Event\AbstractEvent;
use DavidBadura\GitWebhooks\Event\MergeRequestEvent;
use DavidBadura\GitWebhooks\Event\PushEvent;
use DavidBadura\GitWebhooks\Struct\Commit as EventCommit;
use DavidBadura\GitWebhooks\Struct\Repository as EventRepository;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class WebhookHandler
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EntityManager $em
     * @param EventDispatcherInterface $dispatcher
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManager $em, EventDispatcherInterface $dispatcher, LoggerInterface $logger = null)
    {
        $this->em         = $em;
        $this->dispatcher = $dispatcher ?: new EventDispatcher();
        $this->logger     = $logger ?: new NullLogger();
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param AbstractEvent $event
     *
     * @throws \Exception
     */
    public function handle(AbstractEvent $event)
    {
        if ($event instanceof MergeRequestEvent) {
            $this->handleMergeEvent($event);
        } elseif ($event instanceof PushEvent) {
            if ($event->type == PushEvent::TYPE_BRANCH) {
                $this->handlePushEventTypeBranch($event);
            }
        }
    }

    /**
     * @param MergeRequestEvent $event
     */
    private function handleMergeEvent(MergeRequestEvent $event)
    {
        $project = $this->project($event->repository);
        $commit  = $this->commit($project, $event->sourceRepository, $event->lastCommit);

        $mergeRequest = $this->em->getRepository('AppBundle:MergeRequest')->findMergeRequestByRemote(
            $event->repository->id,
            $event->id
        );

        if (!$mergeRequest) {
            $mergeRequest = new MergeRequest();
            $mergeRequest->setRemoteId($event->id);
            $this->em->persist($mergeRequest);
        }

        $mergeRequest->setProject($project);
        $mergeRequest->setName($event->title);
        $mergeRequest->setStatus($event->state);

        if (!$mergeRequest->getCommits()->contains($commit)) {
            $mergeRequest->getCommits()->add($commit);
            $commit->getMergeRequests()->add($mergeRequest);
        }

        $this->em->flush();
    }

    /**
     * @param PushEvent $event
     */
    private function handlePushEventTypeBranch(PushEvent $event)
    {
        $project = $this->project($event->repository);
        $commit  = $this->commit($project, $event->repository, array_pop($event->commits));


        $branch = $this->em->getRepository('AppBundle:Branch')->findBranchByRemoteId(
            $event->repository->id,
            $event->branchName
        );

        if (!$branch) {
            $branch = new Branch();
            $branch->setName($event->branchName);
            $this->em->persist($branch);
        }

        $branch->setProject($project);

        if (!$branch->getCommits()->contains($commit)) {
            $branch->getCommits()->add($commit);
            $commit->getBranches()->add($branch);
        }

        $this->em->flush();
    }

    /**
     * @param EventRepository $repository
     * @return Project
     */
    private function project(EventRepository $repository)
    {
        if ($project = $this->em->getRepository('AppBundle:Project')->findByRemoteId($repository->id)) {
            return $project;
        }

        $project = new Project();
        $project->setRemoteId($repository->id);
        $project->setName($repository->namespace . '/' . $repository->name);
        $project->setRepositoryUrl($repository->url);
        $project->setWebUrl($repository->homepage);

        $this->em->persist($project);
        $this->em->flush($project);

        return $project;
    }

    /**
     * @param Project $project
     * @param EventRepository $repository
     * @param EventCommit $struct
     * @return Commit
     */
    private function commit(Project $project, EventRepository $repository, EventCommit $struct)
    {
        if ($commit = $this->em->getRepository('AppBundle:Commit')->findCommitByProject($project, $struct->id)) {
            return $commit;
        }

        $commit = new Commit();
        $commit->setGitRepository($repository->url);
        $commit->setProject($project);
        $commit->setRevision($struct->id);

        $this->em->persist($commit);

        return $commit;
    }
}
