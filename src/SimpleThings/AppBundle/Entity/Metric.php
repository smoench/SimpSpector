<?php

namespace SimpleThings\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SimpSpector\Analyser\Metric as AnalyserMetric;

/**
 * @author David Badura <d.a.badura@gmail.com>
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Metric
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $code;

    /**
     * @var int
     *
     * @ORM\Column(type="float")
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $description;

    /**
     * @var Commit
     *
     * @ORM\ManyToOne(targetEntity="SimpleThings\AppBundle\Entity\Commit", inversedBy="issues")
     */
    private $commit;

    /**
     * @param string $title
     * @param string $code
     * @param int $value
     */
    public function __construct($title, $code, $value)
    {
        $this->title  = $title;
        $this->code   = $code;
        $this->value  = $value;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return Commit
     */
    public function getCommit()
    {
        return $this->commit;
    }

    /**
     * @param Commit $commit
     */
    public function setCommit(Commit $commit)
    {
        $this->commit = $commit;
    }

    /**
     * @param AnalyserMetric $metric
     * @return Issue
     */
    public static function createFromAnalyser(AnalyserMetric $metric)
    {
        $obj = new self($metric->getTitle(), $metric->getCode(), $metric->getValue());
        $obj->setDescription($metric->getDescription());

        return $obj;
    }
}