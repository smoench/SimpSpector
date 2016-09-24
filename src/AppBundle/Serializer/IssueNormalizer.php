<?php

namespace AppBundle\Serializer;

use League\CommonMark\Converter;
use SimpSpector\Analyser\Issue;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author David Badura <david.badura@i22.de>
 */
class IssueNormalizer implements NormalizerInterface
{
    /**
     * @var Converter
     */
    private $converter;

    public function __construct(Converter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * @param Issue $object
     * @param null $format
     * @param array $context
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'extraInformation' => $object->getExtraInformation(),
            'title' => $object->getTitle(),
            'description' => $object->getDescription(),
            'description_html' => $this->converter->convertToHtml($object->getDescription()),
            'file' => $object->getFile(),
            'gadget' => $object->getGadget(),
            'level' => $object->getLevel(),
            'line' => $object->getLine(),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Issue;
    }
}