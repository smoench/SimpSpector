<?php

namespace SimpleThings\AppBundle\Tests\Gadget\SimpSpectorGadget;

use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Gadget\FunctionBlacklist;
use SimpleThings\AppBundle\Workspace;

/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */
class SimpSpectorGadgetTest extends \PHPUnit_Framework_TestCase
{
    private function createIssue($message, $line, $level)
    {
        $issue = new Issue($message, FunctionBlacklist::NAME, $level);
        $issue->setFile('foo.php');
        $issue->setLine($line);

        return $issue;
    }

    public function testDefaultConfig()
    {
        $workspace         = new Workspace();
        $workspace->path   = __DIR__ . '/_data';
        $workspace->config = [FunctionBlacklist::NAME => []];

        $gadget = new FunctionBlacklist();
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

    public function testExitDie()
    {
        $workspace         = new Workspace();
        $workspace->path   = __DIR__ . '/_data';
        $workspace->config = [FunctionBlacklist::NAME => ['blacklist' => ['die' => 'critical']]];

        $gadget = new FunctionBlacklist();
        $issues = $gadget->run($workspace);

        $expectedIssues = [
            $this->createIssue('function / statement "die/exit" is blacklisted', 37, Issue::LEVEL_CRITICAL),
            $this->createIssue('function / statement "die/exit" is blacklisted', 39, Issue::LEVEL_CRITICAL),
        ];

        $this->assertEquals($expectedIssues, $issues);

        $workspace->config = [FunctionBlacklist::NAME => ['blacklist' => ['exit' => 'critical']]];

        $issues = $gadget->run($workspace);

        $expectedIssues = [
            $this->createIssue('function / statement "die/exit" is blacklisted', 37, Issue::LEVEL_CRITICAL),
            $this->createIssue('function / statement "die/exit" is blacklisted', 39, Issue::LEVEL_CRITICAL),
        ];

        $this->assertEquals($expectedIssues, $issues);
    }

    public function testNormalFunction()
    {
        $workspace         = new Workspace();
        $workspace->path   = __DIR__ . '/_data';
        $workspace->config = [FunctionBlacklist::NAME => ['blacklist' => ['extra_var_dump' => 'warning']]];

        $gadget = new FunctionBlacklist();
        $issues = $gadget->run($workspace);

        $expectedIssues = [
            $this->createIssue('function / statement "extra_var_dump" is blacklisted', 47, Issue::LEVEL_WARNING),
        ];

        $this->assertEquals($expectedIssues, $issues);
    }
}
