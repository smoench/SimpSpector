<?php

namespace SimpleThings\AppBundle;

use Doctrine\ORM\EntityManager;
use SimpleThings\AppBundle\Entity\Commit;
use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Entity\Metric;
use SimpleThings\AppBundle\Logger\LoggerFactory;
use SimpSpector\Analyser\Executor\ExecutorInterface;
use SimpSpector\Analyser\Loader\LoaderInterface;
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
     * @var GitCheckout
     */
    private $gitCheckout;

    /**
     * @var LoaderInterface
     */
    private $configLoader;

    /**
     * @var ExecutorInterface
     */
    private $gadgetExecutor;

    /**
     * @var LoggerFactory
     */
    private $loggerFactory;

    /**
     * @param EntityManager $em
     * @param GitCheckout $gitCheckout
     * @param LoaderInterface $loader
     * @param ExecutorInterface $gadgetExecutor
     * @param LoggerFactory $loggerFactory
     */
    public function __construct(
        EntityManager $em,
        GitCheckout $gitCheckout,
        LoaderInterface $loader,
        ExecutorInterface $gadgetExecutor,
        LoggerFactory $loggerFactory
    ) {
        $this->em              = $em;
        $this->gitCheckout     = $gitCheckout;
        $this->gadgetExecutor  = $gadgetExecutor;
        $this->configLoader    = $loader;
        $this->loggerFactory   = $loggerFactory;
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

            $workspace = $this->gitCheckout->create($commit, $logger);

            $workspace->config = $this->configLoader->load($workspace->path . '/.simpspector.yml');
            $this->execute($commit, $workspace, $logger);

            $commit->setStatus(Commit::STATUS_SUCCESS);
            $this->em->flush($commit);
            $this->gitCheckout->remove($workspace);

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
     * @param Workspace $workspace
     * @param AbstractLogger $logger
     */
    private function execute(Commit $commit, Workspace $workspace, AbstractLogger $logger)
    {
        $commit->setGadgets(array_keys($workspace->config));

        $result = $this->gadgetExecutor->run($workspace->path, $workspace->config, $logger);

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
