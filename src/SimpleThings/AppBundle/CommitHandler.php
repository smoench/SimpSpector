<?php

namespace SimpleThings\AppBundle;

use SimpleThings\AppBundle\Entity\Commit;
use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Exception\MissingSimpSpectorConfigException;

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
     * @param GitCheckout $gitCheckout
     * @param ConfigLoader $loader
     * @param GadgetExecutor $gadgetExecutor
     * @param SyntaxHighlighter $highlighter
     */
    public function __construct(
        GitCheckout $gitCheckout,
        ConfigLoader $loader,
        GadgetExecutor $gadgetExecutor,
        SyntaxHighlighter $highlighter
    )
    {
        $this->gitCheckout    = $gitCheckout;
        $this->gadgetExecutor = $gadgetExecutor;
        $this->configLoader   = $loader;
        $this->highlighter    = $highlighter;
    }

    /**
     * @param Commit $commit
     */
    public function handle(Commit $commit)
    {
        $workspace = $this->gitCheckout->create($commit);

        try {
            $workspace->config = $this->configLoader->load($workspace);
        } catch (\Exception $e) {
            $issue = new Issue($e->getMessage(), 'simpspector', Issue::LEVEL_CRITICAL);
            $issue->setCommit($commit);
            $commit->getIssues()->add($issue);
            if ($e instanceof MissingSimpSpectorConfigException) {
                $issue->setFile('simpspector.yml');
            }

            $this->gitCheckout->remove($workspace);

            return;
        }

        $this->execute($commit, $workspace);
    }

    /**
     * @param Commit $commit
     * @param Workspace $workspace
     */
    private function execute(Commit $commit, Workspace $workspace)
    {
        $commit->setGadgets(array_keys($workspace->config));

        $issues = $this->gadgetExecutor->run($workspace);

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

        $this->gitCheckout->remove($workspace);
    }
} 
