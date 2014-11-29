<?php

namespace SimpleThings\AppBundle;

use SimpleThings\AppBundle\Entity\Commit;

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
    ) {
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
        $workspace         = $this->gitCheckout->create($commit);
        $workspace->config = $this->configLoader->load($workspace);

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
    }
} 
