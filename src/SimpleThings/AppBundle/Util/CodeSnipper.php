<?php

namespace SimpleThings\AppBundle\Util;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class CodeSnipper
{
    /**
     * @param string $file
     * @param int $line
     * @param int $around
     * @return string
     */
    public static function snip($file, $line, $around = 5)
    {
        $source = file_get_contents($file);
        $rows   = explode(PHP_EOL, $source);

        $offset = max($line - $around, 1) - 1;
        $length = $around * 2 + 1;
        if ($line + $length > count($rows)) {
            $length = count($rows) - $offset;
        }

        $slicedSource = array_slice($rows, $offset, $length);

        return implode(PHP_EOL, $slicedSource);
    }
}
