<?php

namespace AppBundle\CommonMark\Block\Parser;

use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\Block\Parser\AbstractBlockParser;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

/**
 * @author David Badura <d.a.badura@gmail.com>
 * @author Simon Mönch <simonmoench@gmail.com>
 */
class FencedCodeParser extends AbstractBlockParser
{
    /**
     * @param ContextInterface $context
     * @param Cursor           $cursor
     *
     * @return bool
     */
    public function parse(ContextInterface $context, Cursor $cursor)
    {
        if ($cursor->isIndented()) {
            return false;
        }

        $previousState = $cursor->saveState();
        $indent = $cursor->advanceToFirstNonSpace();
        $fence = $cursor->match('/^`{3,}(?!.*`)|^~{3,}(?!.*~)/');

        if (null === $fence) {
            $cursor->restoreState($previousState);

            return false;
        }

        $attrs = explode(' ', $cursor->getRemainder());
        $blockAttributes = [];

        foreach ($attrs as $attr) {
            $attr = explode(':', $attr, 2);

            if (2 !== count($attr)) {
                continue;
            }

            list($key, $value) = $attr;

            switch ($key) {
                case 'line':
                    $blockAttributes['class'] = 'line-numbers';
                    $blockAttributes['data-line'] = $value;
                    break;
                case 'file':
                    $blockAttributes['data-file'] = $value;
                    break;
                case 'offset':
                    $blockAttributes['data-start'] = $value;
                    $blockAttributes['data-line-offset'] = $value - 1;
                    break;
            }
        }

        // fenced code block
        $fenceLength = strlen($fence);
        $block = new FencedCode($fenceLength, $fence[0], $indent);
        $block->data['attributes'] = $blockAttributes;

        $context->addBlock($block);

        return true;
    }
}
