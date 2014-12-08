<?php

namespace SimpleThings\AppBundle\Gadget;

use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Workspace;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Lars Wallenborn <lars@wallenborn.net>
 */
class CommentBlacklistGadget extends AbstractGadget
{
    const NAME = 'comment_blacklist';

    /**
     * @param Workspace $workspace
     * @return Issue[]
     */
    public function run(Workspace $workspace)
    {
        $options = $this->prepareOptions(
            (array)$workspace->config[self::NAME],
            [
                'files'     => './',
                'blacklist' => [
                    'todo'        => Issue::LEVEL_NOTICE,
                    'dont commit' => Issue::LEVEL_ERROR,
                ]
            ],
            ['files', 'blacklist']
        );
        $issues  = [];
        foreach ($this->findFiles($workspace->path, $options['files']) as $filename) {
            $issues = array_merge($this->processFile($filename, $options), $issues);
        }

        return $issues;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @param string $filename
     * @param array $options
     * @return Issue[]
     */
    private function processFile($filename, array $options)
    {
        $comments = $this->extract($filename);
        $issues   = [];
        foreach ($comments as $comment) {
            $issues = array_merge($issues, $this->processComment($filename, $options, $comment));
        }

        return $issues;
    }

    /**
     * @param string $filename
     * @return array
     */
    private function extract($filename)
    {
        $allTokens     = token_get_all(file_get_contents($filename));
        $commentTokens = array_filter($allTokens, function ($token) {
            return (count($token) === 3) && (in_array($token[0], [372 /* T_COMMENT */, 373 /* T_DOC_COMMENT */]));
        });

        return array_map(function ($comment) {
            return [
                'content' => $comment[1],
                'line'    => $comment[2],
            ];
        }, $commentTokens);
    }

    /**
     * @param $filename
     * @param array $options
     * @param $comment
     * @return array
     */
    private function processComment($filename, array $options, $comment)
    {
        $issues = [];
        foreach ($options['blacklist'] as $needle => $level) {
            $segment = explode($needle, $comment['content']);
            array_pop($segment); // $segment has n+1 elements if there are n $needel s.

            $offset = 0;
            foreach ($segment as $s) {
                $offset += count(explode("\n", $s)) - 1; // calculate the exact line number of the issue

                $issue = new Issue(sprintf('found "%s" in a comment', $needle), $this->getName(), $level);
                $issue->setFile($filename);
                $issue->setLine($comment['line'] + $offset);

                $issues[] = $issue;
            }
        }

        return $issues;
    }
}
