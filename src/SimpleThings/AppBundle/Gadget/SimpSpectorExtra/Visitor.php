<?php
namespace SimpleThings\AppBundle\Gadget\SimpSpectorExtra;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use SimpleThings\AppBundle\Entity\Issue;

/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */
class Visitor extends NodeVisitorAbstract
{
    private $currentFile;
    private $issues;
    private $lastNode;

    public function __construct(array $options)
    {
        // @todo parse options
        $this->issues = [];
    }

    public function setCurrentFile($file)
    {
        $this->currentFile = $file;
    }

    public function leaveNode(Node $node)
    {
        if (
            $node instanceof Node\Name &&
            $this->lastNode instanceof Node\Expr\FuncCall &&
            $node->getFirst() == 'var_dump'
        ) {
            $this->addIssue('var_dump calls should be avoided', $node, Issue::LEVEL_ERROR);
        }

        if ($node instanceof Node\Expr\Exit_) {
            $this->addIssue('die/exit calls should be avoided', $node, Issue::LEVEL_ERROR);
        }

        if ($node instanceof Node\Stmt\Echo_) {
            $this->addIssue('echo statements should be avoided', $node, Issue::LEVEL_WARNING);
        }

        $this->lastNode = $node;
    }

    public function getIssues()
    {
        return $this->issues;
    }

    public function addException(\Exception $error)
    {
        $this->addIssue('Exception: ' . $error->getMessage(), null, Issue::LEVEL_CRITICAL);
    }

    private function addIssue($message, Node $node = null, $level = Issue::LEVEL_ERROR)
    {
        $issue = new Issue($message, 'extra', $level);
        $issue->setFile($this->currentFile);

        if ($node) {
            $issue->setLine($node->getLine());
        }

        $this->issues[] = $issue;
    }
}
