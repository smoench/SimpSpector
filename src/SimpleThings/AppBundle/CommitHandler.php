<?php

namespace SimpleThings\AppBundle;

use Doctrine\ORM\EntityManager;
use SimpleThings\AppBundle\Entity\Commit;
use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Entity\Metric;
use SimpleThings\AppBundle\Event\CommitEvent;
use SimpleThings\AppBundle\Event\CommitExceptionEvent;
use SimpleThings\AppBundle\Event\CommitResultEvent;
use SimpleThings\AppBundle\Logger\LoggerFactory;
use SimpSpector\Analyser\Analyser;
use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Result;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class CommitHandler
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var WorkspaceManager
     */
    private $workspaceManager;

    /**
     * @var Analyser
     */
    private $analyser;

    /**
     * @var LoggerFactory
     */
    private $loggerFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EntityManager $em
     * @param WorkspaceManager $workspaceManager
     * @param Analyser $analyser
     * @param LoggerFactory $loggerFactory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManager $em,
        WorkspaceManager $workspaceManager,
        Analyser $analyser,
        LoggerFactory $loggerFactory,
        EventDispatcherInterface $eventDispatcher = null
    ) {
        $this->em               = $em;
        $this->workspaceManager = $workspaceManager;
        $this->analyser         = $analyser;
        $this->loggerFactory    = $loggerFactory;
        $this->eventDispatcher  = ($eventDispatcher) ?: new EventDispatcher();
    }

    /**
     * @param Commit $commit
     * @return bool status
     */
    public function handle(Commit $commit)
    {
        $logger = $this->loggerFactory->createLogger($commit);
        $this->startProcess($commit);

        try {
            $event = new CommitEvent($commit, $logger);
            $this->eventDispatcher->dispatch(Events::BEGIN, $event);

            $path = $this->workspaceManager->create($commit, $logger);

            $result = $this->execute($commit, $path, $logger);

            $this->workspaceManager->cleanUp($commit);

            $event = new CommitResultEvent($commit, $logger, $result);
            $this->eventDispatcher->dispatch(Events::RESULT, $event);
        } catch (\Exception $e) {
            $event = new CommitExceptionEvent($commit, $logger, $e);
            $this->eventDispatcher->dispatch(Events::EXCEPTION, $event);

            $commit->setStatus(Commit::STATUS_ERROR);
            $this->em->flush($commit);
        }
    }

    /**
     * @param Commit $commit
     * @param string $path
     * @param AbstractLogger $logger
     * @return Result
     */
    private function execute(Commit $commit, $path, AbstractLogger $logger)
    {
        $result = $this->analyser->analyse($path, null, $logger);

        foreach ($result->getIssues() as $issue) {
            $entity = Issue::createFromAnalyser($issue);

            $entity->setCommit($commit);
            $commit->getIssues()->add($entity);
        }

        foreach ($result->getMetrics() as $metric) {
            $entity = Metric::createFromAnalyser($metric);

            $entity->setCommit($commit);
            $commit->getMetrics()->add($entity);
        }

        $commit->setStatus(Commit::STATUS_SUCCESS);
        $this->em->flush($commit);

        return $result;
    }

    /**
     * @param Commit $commit
     */
    private function startProcess(Commit $commit)
    {
        foreach ($commit->getIssues() as $issue) {
            $this->em->remove($issue);
        }

        $commit->getIssues()->clear();
        $commit->getMetrics()->clear();
        $commit->setStatus(Commit::STATUS_RUN);

        $this->em->flush($commit);
    }
}
