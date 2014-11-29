<?php
/**
 *
 */

namespace SimpleThings\AppBundle;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class SyntaxHighlighter
{
    /**
     * @param string $file
     * @return string
     */
    public function highlight($file)
    {
        $geshi = $this->createGeshi(file_get_contents($file));

        return $geshi->parse_code();
    }

    /**
     * @param string $file
     * @param int $line
     * @param int $around
     * @return string
     */
    public function highlightAroundLine($file, $line, $around = 5)
    {
        $source = file_get_contents($file);
        $rows = explode(PHP_EOL, $source);

        $offset = max($line - $around, 1) - 1;
        $length = $around * 2 + 1;
        if ($line + $length > count($rows)) {
            $length = count($rows) - $offset;
        }

        $slicedSource = array_slice($rows, $offset, $length);
        $slicedSource = implode(PHP_EOL, $slicedSource);

        $geshi = $this->createGeshi($slicedSource);
        $geshi->start_line_numbers_at($offset + 1);
        $geshi->highlight_lines_extra([$line - $offset]);

        return $geshi->parse_code();
    }

    private function createGeshi($source)
    {
        $geshi = new \GeSHi($source, 'php');
        $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);

        return $geshi;
    }

}
