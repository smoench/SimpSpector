<?php

namespace SimpleThings\AppBundle\Tests\Gadget;

use SimpleThings\AppBundle\Gadget\CommentBlacklistGadget;
use SimpleThings\AppBundle\Logger\NullLogger;
use SimpleThings\AppBundle\Workspace;

/**
 * @author Lars Wallenborn <lars@wallenborn.net>
 */
class CommentBlacklistGadgetTest extends \PHPUnit_Framework_TestCase
{
    /** @var CommentBlacklistGadget */
    private $OUT;

    protected function setUp()
    {
        $this->OUT = new CommentBlacklistGadget();
    }

    public function testFixtures()
    {
        $workspace = new Workspace();

        $workspace->path   = __DIR__ . '/_data/comment_blacklist';
        $workspace->config = ['comment_blacklist' => []];

        $issues = $this->OUT->run($workspace, new NullLogger())->getIssues();

        $this->assertEquals(5, count($issues));
        $lineNumbers = [];
        foreach ($issues as $issue) {
            $this->assertStringEndsWith('foo.php', $issue->getFile());
            $lineNumbers[] = $issue->getLine();
        }
        $this->assertEquals([11, 19, 21, 23, 27], $lineNumbers);
    }
} 