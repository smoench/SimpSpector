<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Twig;

use SimpleThings\AppBundle\SyntaxHighlighter;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class HighlightExtension extends \Twig_Extension
{
    /**
     * @var SyntaxHighlighter
     */
    private $highlighter;

    /**
     * @param SyntaxHighlighter $highlighter
     */
    public function __construct(SyntaxHighlighter $highlighter)
    {
        $this->highlighter = $highlighter;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            'highlight' => new \Twig_SimpleFunction('highlight', [$this, 'highlight'], ['is_safe' => ['html']])
        ];
    }

    /**
     * @param string $file
     * @param int|null $line
     * @param int $around
     * @return string
     */
    public function highlight($file, $line = null, $around = 5)
    {
        if ($line !== null) {
            return $this->highlighter->highlightAroundLine($file, $line, $around);
        }

        return $this->highlighter->highlight($file);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'highlight';
    }
}
