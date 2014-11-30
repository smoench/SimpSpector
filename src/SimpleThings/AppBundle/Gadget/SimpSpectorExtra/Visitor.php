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
    private $blacklist;

    public function __construct(array $blacklist)
    {
        $this->blacklist = $blacklist;
        $this->issues  = [];
    }

    public function setCurrentFile($file)
    {
        $this->currentFile = $file;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Name && isset($this->blacklist[$node->getFirst()])) {
            $this->addIssueForBlacklistedFunction(
                $node->getFirst(),
                $node,
                $this->translateErrorLevel($this->blacklist[$node->getFirst()])
            );
        }

        if (isset($this->blacklist['die']) && $node instanceof Node\Expr\Exit_) {
            $this->addIssueForBlacklistedFunction(
                'die/exit',
                $node,
                $this->translateErrorLevel($this->blacklist['die'])
            );
        } elseif (isset($this->blacklist['exit']) && $node instanceof Node\Expr\Exit_) {
            $this->addIssueForBlacklistedFunction(
                'die/exit',
                $node,
                $this->translateErrorLevel($this->blacklist['exit'])
            );
        }

        if (isset($this->blacklist['echo']) && $node instanceof Node\Stmt\Echo_) {
            $this->addIssueForBlacklistedFunction(
                'echo',
                $node,
                $this->translateErrorLevel($this->blacklist['echo'])
            );
        }
    }

    public function getIssues()
    {
        return $this->issues;
    }

    public function addException(\Exception $error)
    {
        $this->addIssue('Exception: ' . $error->getMessage(), null, Issue::LEVEL_CRITICAL);
    }

    private function addIssueForBlacklistedFunction($function, Node $node, $level)
    {
        $this->addIssue(
            sprintf('function / statement "%s" is blacklisted', $function),
            $node,
            $level
        );
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

    private function translateErrorLevel($string)
    {
        switch(trim(strtolower($string))) {
            case 'notice':
                return Issue::LEVEL_NOTICE;
            case 'warning':
                return Issue::LEVEL_WARNING;
            case 'error':
                return Issue::LEVEL_ERROR;
            case 'critical':
                return Issue::LEVEL_CRITICAL;
            default:
                throw new \RuntimeException('unknown error level ' . $string);
        }
    }
}
