<?php

namespace SimpleThings\AppBundle\Util;

use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Workspace;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class HighlightHelper
{
    /**
     * @param Workspace $workspace
     * @param Issue $issue
     * @param int $around
     * @return string
     */
    public static function createCodeSnippet(Workspace $workspace, Issue $issue, $around = 5)
    {
        $snippet = SnippetHelper::createSnippetByFile(
            $workspace->path . '/' . $issue->getFile(),
            $issue->getLine(),
            $around
        );

        $extension = pathinfo($issue->getFile(), PATHINFO_EXTENSION);
        $offset    = max($issue->getLine() - $around, 1);

        $options = [
            'file'   => $issue->getFile(),
            'line'   => $issue->getLine(),
            'offset' => $offset
        ];

        return (new MarkdownBuilder())->code($snippet, $extension, $options)->getMarkdown();
    }
}
