<?php

namespace SimpleThings\AppBundle;

use Doctrine\ORM\EntityManager;
use SimpleThings\AppBundle\Entity\Commit;
use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Entity\Metric;
use SimpleThings\AppBundle\Logger\LoggerFactory;
use SimpSpector\Analyser\Analyser;
use SimpSpector\Analyser\Logger\AbstractLogger;

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
     * @param EntityManager $em
     * @param WorkspaceManager $workspaceManager
     * @param Analyser $analyser
     * @param LoggerFactory $loggerFactory
     */
    public function __construct(
        EntityManager $em,
        WorkspaceManager $workspaceManager,
        Analyser $analyser,
        LoggerFactory $loggerFactory
    ) {
        $this->em               = $em;
        $this->workspaceManager = $workspaceManager;
        $this->analyser         = $analyser;
        $this->loggerFactory    = $loggerFactory;
    }

    /**
     * @param Commit $commit
     * @return bool status
     */
    public function handle(Commit $commit)
    {
        $logger = $this->loggerFactory->createLogger($commit);

        try {
            $this->startProcess($commit);

            $path = $this->workspaceManager->create($commit, $logger);

            $this->execute($commit, $path, $logger);

            $commit->setStatus(Commit::STATUS_SUCCESS);
            $this->em->flush($commit);
            $this->workspaceManager->cleanUp($path);

            $logger->writeln("");
            $logger->writeln("finish :)");

        } catch (\Exception $e) {

            $logger->writeln();
            $logger->writeln(">> EXCEPTION <<");
            $logger->writeln();

            $logger->writeln($e->getMessage());
            $logger->writeln($e->getTraceAsString());

            $commit->setStatus(Commit::STATUS_ERROR);
            $this->em->flush($commit);
        }
    }

    /**
     * @param Commit $commit
     * @param string $path
     * @param AbstractLogger $logger
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
