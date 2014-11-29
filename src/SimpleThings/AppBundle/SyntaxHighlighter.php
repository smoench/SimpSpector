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
     * @var \GeSHi
     */
    protected $geshi;

    /**
     *
     */
    public function __construct()
    {
        $this->geshi = new \GeSHi('', 'php');
        $this->geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS);
    }

    /**
     * @param string $file
     * @return string
     */
    public function highlight($file)
    {
        $this->geshi->set_source(file_get_contents($file));

        return $this->geshi->parse_code();
    }

    /**
     * @param string $file
     * @param int $line
     * @param int $around
     * @return string
     */
    public function highlightAroundLine($file, $line, $around = 5)
    {
        $source = $this->getSlicedSource($file, $line, $around);

        $this->geshi->set_source($source);
        $this->geshi->start_line_numbers_at($line - $around);
        $this->geshi->highlight_lines_extra([$line - $around + 1]);

        return $this->geshi->parse_code();
    }

    /**
     * @param string $file
     * @param int $line
     * @param int $around
     * @return string
     */
    private function getSlicedSource($file, $line, $around = 5)
    {
        $source = file_get_contents($file);

        $rows = explode(PHP_EOL, $source);

        $offest = max($line - $around - 1, 0);
        $length = min($around * 2 + 1, count($rows));

        $slicedSource = array_slice($rows, $offest, $length);

        return implode(PHP_EOL, $slicedSource);
    }
} 