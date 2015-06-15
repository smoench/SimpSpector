<?php

namespace SimpleThings\AppBundle\Entity;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Metric;
use SimpSpector\Analyser\Result as BaseResult;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Result extends BaseResult
{
    /**
     * @param BaseResult $result
     */
    public function __construct(BaseResult $result)
    {
        $issues  = $result->getIssues();
        $metrics = $this->prepareMetrics($result->getMetrics());

        parent::__construct($issues, $metrics);
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

    private function serializeMetrics()
    {
        return $this->getSerializer()->serialize($this->getIssues(), 'json');
    }

    private function deserializeMetrics()
    {

    }

    private function serializeIssues()
    {

    }

    private function deserializeIssues()
    {

    }

    /**
     * @return Serializer
     */
    private function getSerializer()
    {
        return SerializerBuilder::create()->build();
    }
}