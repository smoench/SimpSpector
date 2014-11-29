<?php

namespace SimpleThings\AppBundle\Tests\Gadget\SimpSpectorGadget;

use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Gadget\SimpSpectorExtra;
use SimpleThings\AppBundle\Workspace;

/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */
class SimpSpectorGadgetTest extends \PHPUnit_Framework_TestCase
{
    private function createIssue($message, $line, $level)
    {
        $issue = new Issue($message, 'extra', $level);
        $issue->setFile(__DIR__ . '/_data/foo.php');
        $issue->setLine($line);

        return $issue;
    }

    public function testFoo()
    {
        $workspace         = new Workspace();
        $workspace->path   = __DIR__ . '/_data';
        $workspace->config = ['extra' => []];

        $gadget = new SimpSpectorExtra();
        $issues = $gadget->run($workspace);

        $expectedIssues = [
            $this->createIssue('echo statements should be avoided', 9, Issue::LEVEL_WARNING),
            $this->createIssue('echo statements should be avoided', 13, Issue::LEVEL_WARNING),
            $this->createIssue('unfinished todo', 19, Issue::LEVEL_WARNING),
            $this->createIssue('unfinished todo', 21, Issue::LEVEL_WARNING),
            $this->createIssue('unfinished todo', 23, Issue::LEVEL_WARNING),
            $this->createIssue('unfinished todo', 27, Issue::LEVEL_WARNING),
            $this->createIssue('var_dump calls should be avoided', 32, Issue::LEVEL_ERROR),
        ];

        $this->assertEquals($expectedIssues, $issues);
    }
}
