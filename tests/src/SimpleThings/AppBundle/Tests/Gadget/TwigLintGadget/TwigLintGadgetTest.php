<?php

namespace SimpleThings\AppBundle\Tests\Gadget\SimpSpectorGadget;

use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Gadget\TwigLintGadget;
use SimpleThings\AppBundle\Logger\NullLogger;
use SimpleThings\AppBundle\Workspace;

/**
 * @author Tobias Olry <tobias.olry@gmail.com>
 */
class TwigLintGadgetTest extends \PHPUnit_Framework_TestCase
{
    private function createIssue($message, $file, $line, $level)
    {
        $issue = new Issue($message, TwigLintGadget::NAME, $level);
        $issue->setFile($file);
        $issue->setLine($line);

        return $issue;
    }

    public function testNoErrors()
    {
        $workspace         = new Workspace();
        $workspace->path   = __DIR__ . '/_data/success';
        $workspace->config = [TwigLintGadget::NAME => []];

        $gadget = new TwigLintGadget();
        $issues = $gadget->run($workspace, new NullLogger())->getIssues();

        $this->assertEquals([], $issues);
    }

    public function testOneLineError()
    {
        $workspace         = new Workspace();
        $workspace->path   = __DIR__ . '/_data/error';
        $workspace->config = [TwigLintGadget::NAME => []];

        $gadget = new TwigLintGadget();
        $issues = $gadget->run($workspace, new NullLogger())->getIssues();

        $expectedIssues = [
            $this->createIssue('Twig_Error_Syntax: Unclosed "block"', 'one_line_error.html.twig', 11, 'error'),
        ];

        $this->assertEquals($expectedIssues, $issues);
    }
}
