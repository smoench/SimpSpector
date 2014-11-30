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
        $issue = new Issue($message, SimpSpectorExtra::NAME, $level);
        $issue->setFile('foo.php');
        $issue->setLine($line);

        return $issue;
    }

    public function testFoo()
    {
        $workspace         = new Workspace();
        $workspace->path   = __DIR__ . '/_data';
        $workspace->config = [SimpSpectorExtra::NAME => []];

        $gadget = new SimpSpectorExtra();
        $issues = $gadget->run($workspace);

        $expectedIssues = [
            $this->createIssue('function / statement "echo" is blacklisted', 9, Issue::LEVEL_WARNING),
            $this->createIssue('function / statement "echo" is blacklisted', 13, Issue::LEVEL_WARNING),
            $this->createIssue('function / statement "var_dump" is blacklisted', 32, Issue::LEVEL_ERROR),
            $this->createIssue('function / statement "die/exit" is blacklisted', 37, Issue::LEVEL_ERROR),
            $this->createIssue('function / statement "die/exit" is blacklisted', 39, Issue::LEVEL_ERROR),
            $this->createIssue('function / statement "var_dump" is blacklisted', 46, Issue::LEVEL_ERROR),
        ];

        $this->assertEquals($expectedIssues, $issues);
    }
}
