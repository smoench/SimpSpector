<?php

namespace SimpleThings\AppBundle\Gadget;

use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Workspace;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Lars Wallenborn <lars@wallenborn.net>
 */
class CommentCommenterGadget extends AbstractGadget
{
    /**
     * @param Workspace $workspace
     * @return Issue[]
     */
    public function run(Workspace $workspace)
    {
        $options = $this->prepareOption((array)$workspace->config['commentCommenter']);
        $issues  = [];
        foreach ($this->findPhpFiles($workspace->path, $options['files']) as $filename) {
            $issues = array_merge($this->processFile($filename, $options), $issues);
        }

        return $issues;
    }

    /**
     * @param array $options
     * @return array
     */
    private function prepareOption(array $options)
    {
        $resolver = new OptionsResolver();

        $resolver->setDefaults([
            'files'     => './',
            'blacklist' => [
                'todo'       => Issue::LEVEL_WARNING,
                'dirty hack' => Issue::LEVEL_WARNING,
            ]
        ]);

        $ensureArray = function (Options $options, $value) {
            return is_array($value) ? $value : [$value];
        };
        $resolver->setNormalizers([
            'files'     => $ensureArray,
            'blacklist' => $ensureArray,
        ]);

        return $resolver->resolve($options);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'comment_commenter';
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
        foreach ($options['blacklist'] as $needle => $level) {
            if (strpos($comments['content'], $needle) === false) {
                continue;
            }

            $issues[] = new Issue('found %s in a comment', $this->getName(), $level);
        }

        return $issues;
    }

    /**
     * @param string $filename
     * @return array
     */
    private function extract($filename)
    {
        return array_map(function ($comment) {
            return [
                'content' => $comment[1],
                'line'    => $comment[2],
            ];
        }, array_filter(token_get_all(file_get_contents($filename)), function ($token) {
            return (count($token) === 3) && (in_array($token[0], [372 /* T_COMMENT */, 373 /* T_DOC_COMMENT */]));
        }));
    }
}
