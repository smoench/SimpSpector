<?php

namespace AppBundle;

use AppBundle\Entity\Branch;
use AppBundle\Entity\Commit;
use AppBundle\Entity\MergeRequest;
use AppBundle\Entity\Project;
use AppBundle\Repository\MergeRequestRepository;
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

    /**
     * @param LoggerInterface $logger
     */
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
        $this->logger->info('new merge request');

        $project = $this->project($event->repository);
        $commit  = $this->commit($project, $event->sourceRepository, $event->lastCommit);

        $mergeRequest = $this
            ->getMergeRequestRepository()
            ->findMergeRequestByRemote($event->repository->id, $event->id);

        if (! $mergeRequest) {
            $this->logger->info('merge request not found. create...');

            $mergeRequest = new MergeRequest();
            $mergeRequest->setRemoteId($event->id);
            $this->em->persist($mergeRequest);
        }

        $this->logger->info('update merge request...');

        $mergeRequest->setProject($project);
        $mergeRequest->setName($event->title);
        $mergeRequest->setStatus($event->state);
        $mergeRequest->setRemoteId($event->id);
        $mergeRequest->setSourceBranch($event->sourceBranch);
        $mergeRequest->setTargetBranch($event->targetBranch);

        if (! $mergeRequest->getCommits()->contains($commit)) {
            $this->logger->info('add commit into merge request');
            $mergeRequest->getCommits()->add($commit);
            $commit->getMergeRequests()->add($mergeRequest);
        } else {
            $this->logger->info('commit exists already in merge request');
        }

        $this->em->flush();
    }

    /**
     * @param PushEvent $event
     */
    private function handlePushEventTypeBranch(PushEvent $event)
    {
        $this->logger->info('new push request (type branch)');

        $project = $this->project($event->repository);
        $commit  = $this->commit($project, $event->repository, array_pop($event->commits));

        $branch = $this->em->getRepository('AppBundle:Branch')->findBranchByRemoteId(
            $event->repository->id,
            $event->branchName
        );

        if (! $branch) {
            $this->logger->info('branch not found. create...');
            $branch = new Branch();
            $branch->setName($event->branchName);
            $this->em->persist($branch);
        } else {
            $this->logger->info(sprintf('branch with the name "%s" exists already', $event->branchName));
        }

        $this->logger->info('update branch');

        $branch->setProject($project);

        if (! $branch->getCommits()->contains($commit)) {

            $this->logger->info('add commit into branch');

            $branch->getCommits()->add($commit);
            $commit->getBranches()->add($branch);
        } else {
            $this->logger->info('commit exists already in branch');
        }

        $this->em->flush();
    }

    /**
     * @param EventRepository $repository
     *
     * @return Project
     */
    private function project(EventRepository $repository)
    {
        if ($project = $this->em->getRepository('AppBundle:Project')->findByRemoteId($repository->id)) {

            $this->logger->info(sprintf('project with the remote id "%s" exists already', $repository->id));

            return $project;
        }

        $this->logger->info('create project...');

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
     *
     * @return Commit
     */
    private function commit(Project $project, EventRepository $repository, EventCommit $struct)
    {
        if ($commit = $this->em->getRepository('AppBundle:Commit')->findCommitByProject($project, $struct->id)) {
            $this->logger->info(sprintf('commit with the rev "%s" exists already', $struct->id));

            return $commit;
        }

        $this->logger->info('create commit...');

        $commit = new Commit();
        $commit->setGitRepository($repository->url);
        $commit->setProject($project);
        $commit->setRevision($struct->id);

        $this->em->persist($commit);

        return $commit;
    }

    /**
     * @return MergeRequestRepository
     */
    private function getMergeRequestRepository()
    {
        return $this->em->getRepository('AppBundle:MergeRequest');
    }
}
