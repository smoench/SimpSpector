<?php

namespace SimpleThings\AppBundle\Gadget;

/**
 * @author Lars Wallenborn <lars@wallenborn.net>
 */
class CommentCommenterGadget
{
    /**
     * @param string $filename
     * @return string
     */
    public function extract($filename)
    {
        $withComments    = file_get_contents($filename);
        $withoutComments = php_strip_whitespace($filename);
        $comments        = '';

        $j = 0;
        for ($i = 0; $i < strlen($withComments); $i++) {
            if ($withoutComments[$j] === $withComments[$i]) {
                $j++;
            } else {
                $comments .= $withComments[$i];
            }
        }

        return $comments;
    }
}
