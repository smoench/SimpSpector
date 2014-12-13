<?php

namespace SimpleThings\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use SimpleThings\AppBundle\Gadget\Result;

/**
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SimpleThings\AppBundle\Repository\CommitRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Commit
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
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $output;

    /**
     * @var Issue[]
     *
     * @ORM\OneToMany(targetEntity="SimpleThings\AppBundle\Entity\Issue", mappedBy="commit", cascade={"all"})
     */
    private $issues;

    /**
     *
     */
    public function __construct()
    {
        $this->issues  = new ArrayCollection();
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
        if ($this->project) {
            return $this->project;
        }

        return $this->getMergeRequest()->getProject(); // todo remove
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
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param string $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * @return Issue[]|ArrayCollection
     */
    public function getIssues()
    {
        return $this->issues;
    }

    /**
     * @return Result
     */
    public function getResult()
    {
        return new Result($this->issues->toArray());
    }
}
