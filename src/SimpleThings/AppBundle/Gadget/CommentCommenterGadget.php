<?php

namespace SimpleThings\AppBundle\Gadget;

/**
 * @author Lars Wallenborn <lars@wallenborn.net>
 */
class CommentCommenterGadget
{
    /**
     * @param string $filename
     * @return array
     */
    public function extract($filename)
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
