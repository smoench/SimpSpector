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

    /**
     * @param Workspace      $workspace
     * @param AbstractLogger $logger
     * @return Result
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

        $result = new Result();

        foreach ($this->findFiles($workspace->path, $options['files']) as $filename) {
            $result->merge($this->processFile($workspace, $filename, $options));
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @param Workspace $workspace
     * @param string $filename
     * @param array $options
     * @return Result
     */
    private function processFile(Workspace $workspace, $filename, array $options)
    {
        $comments = $this->extract($filename);

        $result = new Result();
        foreach ($comments as $comment) {
            $result->merge($this->processComment($workspace, $filename, $options, $comment));
        }

        return $result;
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
                    [T_COMMENT, T_DOC_COMMENT]
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
     * @param Workspace $workspace
     * @param string $filename
     * @param array $options
     * @param string $comment
     * @return Result
     */
    private function processComment(Workspace $workspace, $filename, array $options, $comment)
    {
        $result = new Result();

        foreach (explode("\n", $comment['content']) as $lineOffset => $line) {
            foreach ($options['blacklist'] as $blacklistedWord => $errorLevel) {

                if (stristr($line, $blacklistedWord) === false) {
                    continue;
                }

                $issue = new Issue(sprintf('found "%s" in a comment', $blacklistedWord), $this->getName(), $errorLevel);
                $issue->setFile($this->cleanupFilePath($workspace, $filename));
                $issue->setLine($comment['line'] + $lineOffset);

                $result->addIssue($issue);
            }
        }

        return $result;
    }
}
