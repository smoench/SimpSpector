<?php

namespace SimpleThings\AppBundle\Tests\Gadget\SimpSpectorGadget;

use SimpleThings\AppBundle\Gadget\CommentCommenterGadget;

/**
 * @author Lars Wallenborn <lars@wallenborn.net>
 */
class CommentCommentatorGadgetTest extends \PHPUnit_Framework_TestCase
{
    /** @var CommentCommenterGadget */
    private $OUT;

    protected function setUp()
    {
        $this->OUT = new CommentCommenterGadget();
    }

    public function testCommentExtractorNoComment()
    {
        $comments = $this->OUT->extract($this->getTestFilename('small-without-comments.php'));
        $this->assertEmpty($comments);
    }

    public function testCommentExtractorComment()
    {
        $comments = $this->OUT->extract($this->getTestFilename('small-with-comments.php'));

        $hasAbc = false;
        $hasDef = false;
        foreach ($comments as $comment) {
            if (strpos($comment['content'], 'ABC') !== false) {
                $hasAbc = true;
                $this->assertEquals(2, $comment['line']);
            }
            if (strpos($comment['content'], 'DEF') !== false) {
                $hasDef = true;
                $this->assertEquals(3, $comment['line']);
            }
        }
        $this->assertEquals(2, count($comments));
        $this->assertTrue($hasAbc);
        $this->assertTrue($hasDef);
    }

    public function testWithClass()
    {
        $comments = $this->OUT->extract($this->getTestFilename('min-with-class.php'));

        $hasAbc = false;
        $hasDef = false;
        foreach ($comments as $comment) {
            if (strpos($comment['content'], 'ABC') !== false) {
                $hasAbc = true;
                $this->assertEquals(6, $comment['line']);
            }
            if (strpos($comment['content'], 'DEF') !== false) {
                $hasDef = true;
                $this->assertEquals(7, $comment['line']);
            }
        }
        $this->assertEquals(2, count($comments));
        $this->assertTrue($hasAbc);
        $this->assertTrue($hasDef);
    }

    public function testCommentsOfFoo()
    {
        $comments = $this->OUT->extract($this->getTestFilename('foo.php'));

        $hasDoSomethingComment = false;
        foreach ($comments as $comment) {
            if (strpos($comment['content'], 'do something here') !== false) {
                $hasDoSomethingComment = true;
                $this->assertEquals(11, $comment['line']);
            }
        }
        $this->assertEquals(2, count($comments));
        $this->assertTrue($hasDoSomethingComment);
    }

    /**
     * @param string $filename
     * @return string
     */
    private function getTestFilename($filename)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . $filename;
    }
} 
