<?php

namespace SimpleThings\AppBundle;

use Doctrine\ORM\EntityManager;
use SimpleThings\AppBundle\Entity\Commit;
use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Exception\MissingSimpSpectorConfigException;
use SimpleThings\AppBundle\Logger\AbstractLogger;
use SimpleThings\AppBundle\Logger\LoggerFactory;

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
     * @var SyntaxHighlighter
     */
    private $highlighter;

    /**
     * @var LoggerFactory
     */
    private $loggerFactory;

    /**
     * @param EntityManager $em
     * @param GitCheckout $gitCheckout
     * @param ConfigLoader $loader
     * @param GadgetExecutor $gadgetExecutor
     * @param SyntaxHighlighter $highlighter
     * @param LoggerFactory $loggerFactory
     */
    public function __construct(
        EntityManager $em,
        GitCheckout $gitCheckout,
        ConfigLoader $loader,
        GadgetExecutor $gadgetExecutor,
        SyntaxHighlighter $highlighter,
        LoggerFactory $loggerFactory
    ) {
        $this->em             = $em;
        $this->gitCheckout    = $gitCheckout;
        $this->gadgetExecutor = $gadgetExecutor;
        $this->configLoader   = $loader;
        $this->highlighter    = $highlighter;
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

            $workspace = $this->gitCheckout->create($commit);

            $this->loadConfiguration($commit, $workspace, $logger);
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

        $issues = $this->gadgetExecutor->run($workspace, $logger);

        foreach ($issues as $issue) {
            $issue->setCommit($commit);

            if ($issue->getFile() && $issue->getLine()) {
                $snippet = $this->highlighter->highlightAroundLine(
                    $workspace->path . '/' . $issue->getFile(),
                    $issue->getLine()
                );

                $issue->setCodeSnippet($snippet);
            }

            $commit->getIssues()->add($issue);
        }
    }

    /**
     * @param Commit $commit
     */
    private function startProcess(Commit $commit)
    {
        foreach($commit->getIssues() as $issue) {
            $this->em->remove($issue);
        }

        $commit->getIssues()->clear();
        $commit->setStatus(Commit::STATUS_RUN);

        $this->em->flush($commit);
    }

    /**
     * @param Commit $commit
     * @param Workspace $workspace
     * @throws MissingSimpSpectorConfigException
     * @throws \Exception
     */
    private function loadConfiguration(Commit $commit, Workspace $workspace)
    {
        try {
            $workspace->config = $this->configLoader->load($workspace);
        } catch (MissingSimpSpectorConfigException $e) {

            $issue = new Issue($e->getMessage(), 'simpspector', Issue::LEVEL_CRITICAL);
            $issue->setCommit($commit);
            $commit->getIssues()->add($issue);
            $issue->setFile('simpspector.yml');

            throw $e;
        }
    }
} 
