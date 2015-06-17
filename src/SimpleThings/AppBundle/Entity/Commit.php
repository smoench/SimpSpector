<?php

namespace SimpleThings\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Metric;

/**
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SimpleThings\AppBundle\Repository\CommitRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Commit implements TimestampableInterface
{
    use Timestampable;

    const STATUS_NEW     = 'new';
    const STATUS_RUN     = 'run';
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR   = 'error';

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
     * @ORM\Column(name="revision", type="string", length=255)
     */
    private $revision;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $gadgets;

    /**
     * @var MergeRequest
     *
     * @ORM\ManyToOne(targetEntity="MergeRequest", inversedBy="commits", cascade={"all"})
     */
    private $mergeRequest;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="commits", cascade={"all"})
     */
    private $project;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @var Result
     *
     * @ORM\Embedded(class="SimpleThings\AppBundle\Entity\Result", columnPrefix="result_")
     */
    private $result;

    /**
     *
     */
    public function __construct()
    {
        $this->status  = self::STATUS_NEW;
        $this->gadgets = [];
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set revision
     *
     * @param string $revision
     */
    public function setRevision($revision)
    {
        $this->revision = $revision;
    }

    /**
     * Get revision
     *
     * @return string
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * @return array
     */
    public function getGadgets()
    {
        return $this->gadgets;
    }

    /**
     * @param array $gadgets
     */
    public function setGadgets($gadgets)
    {
        $this->gadgets = $gadgets;
    }

    /**
     * @return MergeRequest
     */
    public function getMergeRequest()
    {
        return $this->mergeRequest;
    }

    /**
     * @param MergeRequest $mergeRequest
     */
    public function setMergeRequest(MergeRequest $mergeRequest = null)
    {
        $this->mergeRequest = $mergeRequest;
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param Project $project
     */
    public function setProject(Project $project)
    {
        $this->project = $project;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param Result $result
     */
    public function setResult(Result $result = null)
    {
        $this->result = $result;
    }

    /**
     * @return Issue[]
     */
    public function getIssues()
    {
        return $this->result->getIssues();
    }

    /**
     * @return Metric[]
     */
    public function getMetrics()
    {
        return $this->result->getMetrics();
    }

    /**
     * @return Metric[]
     * @deprecated
     */
    public function getIndexedMetrics()
    {
        return $this->getMetrics();
    }

    /**
     * @param string $code
     * @return Metric|null
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
     * @return string
     */
    public function getUniqueId()
    {
        return sprintf(
            "%s_%s",
            $this->getProject()->getId(),
            $this->getRevision()
        );
    }
}
