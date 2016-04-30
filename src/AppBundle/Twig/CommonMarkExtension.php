<?php

namespace AppBundle\Twig;

use League\CommonMark\Converter;

/**
 * @author Simon Mönch <simonmoench@gmail.com>
 */
class CommonMarkExtension extends \Twig_Extension
{
    /**
     * @var Converter
     */
    private $converter;

    public function __construct(Converter $converter)
    {
        $this->converter = $converter;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('commonmark', [$this->converter, 'convertToHtml'], ['is_safe' => ['html']])
        ];
    }

    public function getName()
    {
        return 'simpspector_commonmark';
    }
}