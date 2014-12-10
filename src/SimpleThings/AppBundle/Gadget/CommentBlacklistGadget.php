<?php

namespace SimpleThings\AppBundle\Gadget;

use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Logger\AbstractLogger;
use SimpleThings\AppBundle\Workspace;

/**
 * @author Lars Wallenborn <lars@wallenborn.net>
 */
class CommentBlacklistGadget extends AbstractGadget
{
    const NAME = 'comment_blacklist';

    const T_COMMENT_TOKEN     = 372;
    const T_DOC_COMMENT_TOKEN = 373;

    /**
     * @param Workspace      $workspace
     * @param AbstractLogger $logger
     * @return Issue[]
     */
    public function run(Workspace $workspace, AbstractLogger $logger)
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
     * @param array  $options
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
        $commentTokens = array_filter(
            $allTokens,
            function ($token) {
                return (count($token) === 3) && (in_array(
                    $token[0],
                    [self::T_COMMENT_TOKEN, self::T_DOC_COMMENT_TOKEN]
                ));
            }
        );

        return array_map(
            function ($comment) {
                return [
                    'content' => $comment[1],
                    'line'    => $comment[2],
                ];
            },
            $commentTokens
        );
    }

    /**
     * @param       $filename
     * @param array $options
     * @param       $comment
     * @return array
     */
    private function processComment($filename, array $options, $comment)
    {
        $issues = [];
        foreach (explode("\n", $comment['content']) as $lineOffset => $line) {
            foreach ($options['blacklist'] as $blacklistedWord => $errorLevel) {
                if (stristr($line, $blacklistedWord) === false) {
                    continue;
                }
                $issue = new Issue(sprintf('found "%s" in a comment', $blacklistedWord), $this->getName(), $errorLevel);
                $issue->setFile($filename);
                $issue->setLine($comment['line'] + $lineOffset);
                $issues[] = $issue;
            }
        }

        return $issues;
    }
}
