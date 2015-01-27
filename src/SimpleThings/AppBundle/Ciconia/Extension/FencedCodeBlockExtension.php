<?php

namespace SimpleThings\AppBundle\Ciconia\Extension;

use Ciconia\Common\Text;
use Ciconia\Extension\ExtensionInterface;
use Ciconia\Renderer\RendererAwareInterface;
use Ciconia\Renderer\RendererAwareTrait;
use Ciconia\Markdown;

/**
 * Markdown converts text with four spaces at the front of each line to code blocks.
 * GFM supports that, but we also support fenced blocks.
 * Just wrap your code blocks in ``` and you won't need to indent manually to trigger a code block.
 *
 * PHP Markdown style `~` is also available.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 * @author David Badura <d.a.badura@gmail.com>
 */
class FencedCodeBlockExtension implements ExtensionInterface, RendererAwareInterface
{

    use RendererAwareTrait;

    /**
     * @var Markdown
     */
    private $markdown;

    /**
     * {@inheritdoc}
     */
    public function register(Markdown $markdown)
    {
        $this->markdown = $markdown;

        // should be run before first hashHtmlBlocks
        $markdown->on('initialize', array($this, 'processFencedCodeBlock'));
    }

    /**
     * @param Text $text
     */
    public function processFencedCodeBlock(Text $text)
    {
        /** @noinspection PhpUnusedParameterInspection */
        $text->replace('{
            (?:\n\n|\A)
            (?:
                ([`~]{3})[ ]*         #1 fence ` or ~
                    ([a-zA-Z0-9]*)?  #2 language [optional]
                    ([^\n]*) #3 attr
                \n+
                (.*?)\n                #4 code block
                \1                    # matched #1
            )
        }smx', function (Text $w, Text $fence, Text $lang, Text $attr, Text $code) {

            $options = ['attr' => ['class' => '']];

            if (!$lang->isEmpty()) {
                $options['attr']['class'] .= 'prettyprint lang-' . $lang->lower();
            }

            foreach ($this->extractOptions($attr->getString()) as $key => $value) {
                switch ($key) {
                    case 'line':
                        $options['attr']['class'] .= ' line-numbers';
                        $options['attr']['data-line'] = $value;
                        break;
                    case 'file':
                        $options['attr']['data-file'] = $value;
                        break;
                    case 'offset':
                        $options['attr']['data-start'] = $value;
                        $options['attr']['data-line-offset'] = $value - 1;
                        break;
                }
            }

            $code->escapeHtml(ENT_NOQUOTES);
            $this->markdown->emit('detab', array($code));
            $code->replace('/\A\n+/', '');
            $code->replace('/\s+\z/', '');

            return "\n\n" . $this->getRenderer()->renderCodeBlock($code, $options) . "\n\n";
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'fencedCodeBlock';
    }

    /**
     * @param string $attr
     * @return array
     */
    private function extractOptions($attr)
    {
        $parts   = explode(' ', trim($attr));
        $options = [];

        foreach ($parts as $part) {
            list($key, $value) = explode(':', $part);
            $options[$key] = $value;
        }

        return $options;
    }
}