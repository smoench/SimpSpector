<?php

namespace AppBundle;

use AppBundle\Entity\Branch;
use AppBundle\Entity\Commit;
use AppBundle\Entity\MergeRequest;
use AppBundle\Entity\NewsStreamItem;
use AppBundle\Entity\Project;
use AppBundle\Entity\Tag;
use AppBundle\Repository\BranchRepository;
use AppBundle\Repository\CommitRepository;
use AppBundle\Repository\MergeRequestRepository;
use AppBundle\Repository\ProjectRepository;
use AppBundle\Repository\TagRepository;
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
            } elseif ($event->type == PushEvent::TYPE_TAG) {
                $this->handlePushEventTypeTag($event);
            }
        }
    }

    /**
     * @param MergeRequestEvent $event
     */
    private function handleMergeEvent(MergeRequestEvent $event)
    {
        $this->logger->info(
            sprintf(
                'received merge request from "%s" ("%s") -> "%s" ("%s")',
                $event->sourceBranch,
                $event->sourceRepository->name,
                $event->targetBranch,
                $event->repository->name
            )
        );

        $project = $this->project($event->repository);
        $commit  = $this->commit($project, $event->sourceRepository, $event->lastCommit);

        $mergeRequest = $this
            ->getMergeRequestRepository()
            ->findMergeRequestByRemote($event->repository->id, $event->id);

        if (! $mergeRequest) {
            $this->logger->info('merge request not found. Creating...');

            $mergeRequest = new MergeRequest();
            $mergeRequest->setRemoteId($event->id);
            $this->em->persist($mergeRequest);
        }

        $this->logger->info('updating merge request...');

        $mergeRequest->setProject($project);
        $mergeRequest->setName($event->title);
        $mergeRequest->setStatus($event->state);
        $mergeRequest->setRemoteId($event->id);
        $mergeRequest->setSourceBranch($event->sourceBranch);
        $mergeRequest->setTargetBranch($event->targetBranch);

        if (! $mergeRequest->getCommits()->contains($commit)) {
            $this->logger->info('adding commit into merge request...');
            $mergeRequest->getCommits()->add($commit);
            $commit->getMergeRequests()->add($mergeRequest);
        } else {
            $this->logger->info('commit already exists in merge request');
        }

        $newsStreamItem = new NewsStreamItem();
        $newsStreamItem->setType(NewsStreamItem::TYPE_MERGE_REQUEST);
        $newsStreamItem->setCommit($commit);
        $newsStreamItem->setProject($project);
        $newsStreamItem->setMergeRequest($mergeRequest);

        $this->em->persist($newsStreamItem);

        $this->em->flush();
    }

    /**
     * @param PushEvent $event
     */
    private function handlePushEventTypeBranch(PushEvent $event)
    {
        $this->logger->info(sprintf('received push on branch "%s"', $event->branchName));

        $project = $this->project($event->repository);
        $commit  = $this->commit($project, $event->repository, array_pop($event->commits));

        /** @var BranchRepository $branchRepository */
        $branchRepository = $this->em->getRepository('AppBundle:Branch');

        $branch = $branchRepository->findBranchByRemoteId($event->repository->id, $event->branchName);
        if (! $branch) {
            $this->logger->info('branch not found. Creating...');
            $branch = new Branch();
            $branch->setName($event->branchName);
            $this->em->persist($branch);
        } else {
            $this->logger->info(sprintf('branch with name "%s" already exists', $event->branchName));
        }

        $this->logger->info('updating branch...');

        $branch->setProject($project);

        if (! $branch->getCommits()->contains($commit)) {
            $this->logger->info(sprintf('adding commit to branch "%s"...', $event->branchName));

            $branch->getCommits()->add($commit);
            $commit->getBranches()->add($branch);
        } else {
            $this->logger->info(
                sprintf(
                    'commit "%s" already exists in branch "%s"',
                    $commit->getRevision(),
                    $event->branchName
                )
            );
        }

        $newsStreamItem = new NewsStreamItem();
        $newsStreamItem->setType(NewsStreamItem::TYPE_BRANCH);
        $newsStreamItem->setCommit($commit);
        $newsStreamItem->setProject($project);
        $newsStreamItem->setBranch($branch);
        $this->em->persist($newsStreamItem);
        $this->em->flush();
    }

    /**
     * @param EventRepository $repository
     *
     * @return Project
     */
    private function project(EventRepository $repository)
    {
        /** @var ProjectRepository $projectRepository */
        $projectRepository = $this->em->getRepository('AppBundle:Project');
        if ($project = $projectRepository->findByRemoteId($repository->id)) {
            $this->logger->info(sprintf('project with the remote id "%s" already exists', $repository->id));

            return $project;
        }

        $this->logger->info(
            sprintf(
                'creating project "%s" with remote id "%s"...',
                $repository->name,
                $repository->id
            )
        );

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
        /** @var CommitRepository $commitRepository */
        $commitRepository = $this->em->getRepository('AppBundle:Commit');
        if ($commit = $commitRepository->findCommitByProject($project, $struct->id)) {
            $this->logger->info(sprintf('commit "%s" already exists', $struct->id));

            return $commit;
        }

        $this->logger->info(sprintf('creating commit "%s"...', $struct->id));

        $commit = new Commit();
        $commit->setGitRepository($repository->url);
        $commit->setProject($project);
        $commit->setRevision($struct->id);
        if ($struct->author) {
            $commit->setAuthorName($struct->author->name);
            $commit->setAuthorEmail($struct->author->email);
        }
        $commit->setCommitMessage($struct->message);

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

    /**
     * @param PushEvent $event
     */
    private function handlePushEventTypeTag(PushEvent $event)
    {
        $this->logger->info(sprintf('received push on tag "%s"', $event->tagName));

        $project = $this->project($event->repository);
        $commit  = $this->commit($project, $event->repository, array_pop($event->commits));

        /** @var TagRepository $tagRepository */
        $tagRepository = $this->em->getRepository('AppBundle:Tag');

        $tag = $tagRepository->findTagByRemoteId($event->repository->id, $event->tagName);
        if (! $tag) {
            $this->logger->info('tag not found. Creating...');
            $tag = new Tag();
            $tag->setName($event->tagName);
            $this->em->persist($tag);
        } else {
            $this->logger->info(sprintf('tag with name "%s" already exists', $event->tagName));
        }

        $this->logger->info('updating tag...');

        $tag->setProject($project);

        if ($tag->getCommit() != $commit) {
            $this->logger->info(sprintf('adding commit to tag "%s"...', $event->tagName));

            $tag->setCommit($commit);
            $commit->getTags()->add($tag);
        } else {
            $this->logger->info(
                sprintf(
                    'tag "%s" already points to commit "%s"',
                    $event->tagName,
                    $commit->getRevision()
                )
            );
        }

        $newsStreamItem = new NewsStreamItem();
        $newsStreamItem->setType(NewsStreamItem::TYPE_TAG);
        $newsStreamItem->setCommit($commit);
        $newsStreamItem->setProject($project);
        $newsStreamItem->setTag($tag);
        $this->em->persist($newsStreamItem);
        $this->em->flush();
    }
}
