<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EBT\Compress\GzcompressCompressor as Compressor;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Metric;
use SimpSpector\Analyser\Result as BaseResult;
use Symfony\Component\Serializer\Annotation as JSON;

/**
 * @ORM\Embeddable()
 *
 * @author David Badura <d.a.badura@gmail.com>
 */
class Result extends BaseResult
{
    /**
     * @var string|resource
     *
     * @ORM\Column(type="blob", name="issues", nullable=true)
     */
    protected $serializedIssues;

    /**
     * @var string|resource
     *
     * @ORM\Column(type="blob", name="metrics", nullable=true)
     */
    protected $serializedMetrics;

    /**
     * @param BaseResult $result
     */
    public function __construct(BaseResult $result)
    {
        $issues  = $result->getIssues();
        $metrics = $this->prepareMetrics($result->getMetrics());

        parent::__construct($issues, $metrics);

        $this->serializedIssues  = $this->serialize($issues);
        $this->serializedMetrics = $this->serialize($metrics);
    }

    /**
     * @return Issue[]
     *
     * @JSON\Groups({"commit_full"})
     */
    public function getIssues()
    {
        if ($this->issues === null) {
            $this->issues = $this->deserialize($this->serializedIssues, 'AppBundle\Entity\Issue');
        }

        return $this->issues;
    }

    /**
     * @return Metric[]
     */
    public function getMetrics()
    {
        if ($this->metrics === null) {
            $this->metrics = $this->deserialize($this->serializedMetrics, 'SimpSpector\Analyser\Metric');
        }

        return $this->metrics;
    }

    /**
     * @param string $code
     * @return null|Metric
     */
    public function getMetric($code)
    {
        $metrics = $this->getMetrics();

        return isset($metrics[$code]) ? $metrics[$code] : null;
    }

    /**
     * @param string $code
     * @return bool
     */
    public function hasMetric($code)
    {
        $metrics = $this->getMetrics();

        return isset($metrics[$code]);
    }

    /**
     * @param Issue $issue
     * @throws \Exception
     */
    public function addIssue(Issue $issue)
    {
        throw new \Exception('Readonly!');
    }

    /**
     * @param Metric $metric
     * @throws \Exception
     */
    public function addMetric(Metric $metric)
    {
        throw new \Exception('Readonly!');
    }

    /**
     * @param BaseResult $result
     * @throws \Exception
     */
    public function merge(BaseResult $result)
    {
        throw new \Exception('Readonly!');
    }

    /**
     * @param Metric[] $metrics
     * @return Metric[]
     */
    private function prepareMetrics(array $metrics)
    {
        $return = [];

        foreach ($metrics as $metric) {
            $return[$metric->getCode()] = $metric;
        }

        return $return;
    }

    /**
     * @param array $data
     * @return string
     */
    private function serialize($data)
    {
        $json = $this->getSerializer()->serialize($data, 'json');

        return $this->getCompressor()->compress($json);
    }

    /**
     * @param resource $data
     * @param string $class
     * @return array
     */
    private function deserialize($data, $class)
    {
        if (!is_resource($data)) {
            return [];
        }

        $compressedData = stream_get_contents($data);

        if (!$compressedData) {
            return [];
        }

        $json = $this->getCompressor()->uncompress($compressedData);

        return (array)$this->getSerializer()
            ->deserialize($json, "array<string,$class>", 'json');
    }

    /**
     * @return Serializer
     */
    private function getSerializer()
    {
        return SerializerBuilder::create()->build();
    }

    /**
     * @return Compressor
     */
    private function getCompressor()
    {
        return new Compressor();
    }
}