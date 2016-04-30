<?php

namespace AppBundle\CommonMark\Block\Renderer;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;

/**
 * @author Simon Mönch <simonmoench@gmail.com>
 */
class FencedCodeRenderer implements BlockRendererInterface
{
    /**
     * @param AbstractBlock|FencedCode $block
     * @param ElementRendererInterface $htmlRenderer
     * @param bool                     $inTightList
     *
     * @return HtmlElement
     */
    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = false)
    {
        if (! $block instanceof FencedCode) {
            throw new \InvalidArgumentException('Incompatible block type: ' . get_class($block));
        }

        $attrs = [];
        foreach ($block->getData('attributes', []) as $key => $value) {
            $attrs[$key] = $htmlRenderer->escape($value, true);
        }

        $infoWords = $block->getInfoWords();
        if (! empty($infoWords) && ! empty($infoWords[0])) {
            $attrs['class'] = sprintf(
                'prettyprint lang-%s %s',
                $htmlRenderer->escape($infoWords[0], true),
                (isset($attrs['class']) ? $attrs['class'] : '')
            );
        }

        return new HtmlElement(
            'pre',
            $attrs,
            new HtmlElement('code', [], $htmlRenderer->escape($block->getStringContent()))
        );
    }
}
