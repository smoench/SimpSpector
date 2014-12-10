<?php

namespace SimpleThings\AppBundle;

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
     * @param GitCheckout       $gitCheckout
     * @param ConfigLoader      $loader
     * @param GadgetExecutor    $gadgetExecutor
     * @param SyntaxHighlighter $highlighter
     * @param LoggerFactory     $loggerFactory
     */
    public function __construct(
        GitCheckout $gitCheckout,
        ConfigLoader $loader,
        GadgetExecutor $gadgetExecutor,
        SyntaxHighlighter $highlighter,
        LoggerFactory $loggerFactory
    ) {
        $this->gitCheckout    = $gitCheckout;
        $this->gadgetExecutor = $gadgetExecutor;
        $this->configLoader   = $loader;
        $this->highlighter    = $highlighter;
        $this->loggerFactory  = $loggerFactory;
    }

    /**
     * @param Commit $commit
     */
    public function handle(Commit $commit)
    {
        $workspace = $this->gitCheckout->create($commit);
        $logger = $this->loggerFactory->createLogger($commit);

        try {
            $workspace->config = $this->configLoader->load($workspace);
        } catch (\Exception $e) {
            $issue = new Issue($e->getMessage(), 'simpspector', Issue::LEVEL_CRITICAL);
            $issue->setCommit($commit);
            $commit->getIssues()->add($issue);
            if ($e instanceof MissingSimpSpectorConfigException) {
                $issue->setFile('simpspector.yml');
            }

            $logger->writeln($e->getMessage());

            $this->gitCheckout->remove($workspace);

            return;
        }

        $this->execute($commit, $workspace, $logger);
    }

    /**
     * @param Commit         $commit
     * @param Workspace      $workspace
     * @param AbstractLogger $logger
     */
    private function execute(Commit $commit, Workspace $workspace, AbstractLogger $logger)
    {
        $commit->setGadgets(array_keys($workspace->config));

        $result = $this->gadgetExecutor->run($workspace, $logger);

        foreach ($result->getIssues() as $issue) {
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

        $this->gitCheckout->remove($workspace);
    }
} 
