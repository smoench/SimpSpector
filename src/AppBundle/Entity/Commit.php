<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Metric;

/**
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommitRepository")
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
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $revision;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $gitRepository;

    /**
     * @var MergeRequest[]
     *
     * @ORM\ManyToMany(targetEntity="MergeRequest", inversedBy="commits", cascade={"all"})
     */
    private $mergeRequests;

    /**
     * @var Branch[]
     *
     * @ORM\ManyToMany(targetEntity="Branch", inversedBy="commits", cascade={"all"})
     */
    private $branches;


    /**
     * @var Tag[]
     *
     * @ORM\OneToMany(targetEntity="Tag", mappedBy="commit", cascade={"all"})
     */
    private $tags;

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
     * @ORM\Embedded(class="AppBundle\Entity\Result", columnPrefix="result_")
     */
    private $result;

    /**
     *
     */
    public function __construct()
    {
        $this->mergeRequests = new ArrayCollection();
        $this->branches      = new ArrayCollection();
        $this->status        = self::STATUS_NEW;
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
     * @return string
     */
    public function getGitRepository()
    {
        return $this->gitRepository;
    }

    /**
     * @param string $gitRepository
     */
    public function setGitRepository($gitRepository)
    {
        $this->gitRepository = $gitRepository;
    }

    /**
     * @return MergeRequest[]|ArrayCollection
     */
    public function getMergeRequests()
    {
        return $this->mergeRequests;
    }

    /**
     * @return Tag[]|ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return Branch[]|ArrayCollection
     */
    public function getBranches()
    {
        return $this->branches;
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
     * @return Result
     */
    public function getResult()
    {
        return $this->result;
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
     * @param string $code
     * @return Metric|null
     */
    public function getMetric($code)
    {
        return $this->result->getMetric($code);
    }

    /**
     * @param string $code
     * @return bool
     */
    public function hasMetric($code)
    {
        return $this->result->hasMetric($code);
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
