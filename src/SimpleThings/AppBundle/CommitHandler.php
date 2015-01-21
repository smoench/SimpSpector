<?php

namespace SimpleThings\AppBundle;

use Doctrine\ORM\EntityManager;
use SimpleThings\AppBundle\Entity\Commit;
use SimpleThings\AppBundle\Logger\AbstractLogger;
use SimpleThings\AppBundle\Logger\LoggerFactory;
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
     * @var GitCheckout
     */
    private $gitCheckout;

    /**
     * @var ConfigLoader
     */
    private $configLoader;

    /**
     * @var GadgetExecutor
     */
    private $gadgetExecutor;

    /**
     * @var LoggerFactory
     */
    private $loggerFactory;

    /**
     * @param EntityManager $em
     * @param GitCheckout $gitCheckout
     * @param ConfigLoader $loader
     * @param GadgetExecutor $gadgetExecutor
     * @param LoggerFactory $loggerFactory
     */
    public function __construct(
        EntityManager $em,
        GitCheckout $gitCheckout,
        ConfigLoader $loader,
        GadgetExecutor $gadgetExecutor,
        LoggerFactory $loggerFactory
    ) {
        $this->em             = $em;
        $this->gitCheckout    = $gitCheckout;
        $this->gadgetExecutor = $gadgetExecutor;
        $this->configLoader   = $loader;
        $this->loggerFactory  = $loggerFactory;
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

            $workspace->config = $this->configLoader->load($workspace);
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
        $result = $this->gadgetExecutor->run($workspace, $logger);

        foreach ($result->getIssues() as $issue) {
            $issue->setCommit($commit);
            $commit->getIssues()->add($issue);
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
        $commit->setStatus(Commit::STATUS_RUN);

        $this->em->flush($commit);
    }
} 
