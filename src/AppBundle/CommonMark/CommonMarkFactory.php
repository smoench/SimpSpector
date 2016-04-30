<?php

namespace AppBundle\CommonMark;

use AppBundle\CommonMark\Block\Parser\FencedCodeParser;
use AppBundle\CommonMark\Block\Renderer\FencedCodeRenderer;
use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;

/**
 * @author Simon Mönch <simonmoench@gmail.com>
 */
class CommonMarkFactory
{
    public static function create()
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addBlockParser(new FencedCodeParser());
        $environment->addBlockRenderer(FencedCode::class, new FencedCodeRenderer());

        return new CommonMarkConverter([], $environment);
    }
}