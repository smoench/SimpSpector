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
        $this->assertTrue(! trim($comments));
    }

    public function testCommentExtractorComment()
    {
        $comments = $this->OUT->extract($this->getTestFilename('small-with-comments.php'));
        $this->assertContains('ABC', $comments);
        $this->assertContains('DEF', $comments);
        $this->assertNotContains('$a = ', $comments);
    }

    public function testCommentsOfFoo()
    {
        $comments = $this->OUT->extract($this->getTestFilename('foo.php'));
        $this->assertContains('do something here', $comments);
        $this->assertNotContains('iamok', $comments);
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
