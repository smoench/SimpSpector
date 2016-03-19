<?php

namespace AppBundle\Entity;

use DavidBadura\GitWebhooks\Event\MergeRequestEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MergeRequestRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class MergeRequest implements TimestampableInterface
{
    use Timestampable;

    const STATUS_OPENED = MergeRequestEvent::STATE_OPENED;
    const STATUS_CLOSED = MergeRequestEvent::STATE_CLOSED;
    const STATUS_MERGED = MergeRequestEvent::STATE_MERGED;

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
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $remoteId;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $sourceBranch;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $baseCommit;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $targetBranch;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="mergeRequests", cascade={"all"})
     */
    private $project;

    /**
     * @var Commit[]
     *
     * @ORM\ManyToMany(targetEntity="Commit", mappedBy="mergeRequests", cascade={"all"})
     */
    private $commits;

    /**
     *
     */
    public function __construct()
    {
        $this->commits = new ArrayCollection();
        $this->status  = self::STATUS_OPENED;
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * Set remoteId
     *
     * @param string $remoteId
     */
    public function setRemoteId($remoteId)
    {
        $this->remoteId = $remoteId;
    }

    /**
     * Get remoteId
     *
     * @return string
     */
    public function getRemoteId()
    {
        return $this->remoteId;
    }

    /**
     * Set sourceBranch
     *
     * @param string $sourceBranch
     */
    public function setSourceBranch($sourceBranch)
    {
        $this->sourceBranch = $sourceBranch;
    }

    /**
     * Get sourceBranch
     *
     * @return string
     */
    public function getSourceBranch()
    {
        return $this->sourceBranch;
    }

    /**
     * @return mixed
     */
    public function getTargetBranch()
    {
        return $this->targetBranch;
    }

    /**
     * @param mixed $targetBranch
     */
    public function setTargetBranch($targetBranch)
    {
        $this->targetBranch = $targetBranch;
    }

    /**
     * @return string
     */
    public function getBaseCommit()
    {
        return $this->baseCommit;
    }

    /**
     * @param string $baseCommit
     */
    public function setBaseCommit($baseCommit)
    {
        $this->baseCommit = $baseCommit;
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
     * @return Commit[]|ArrayCollection
     */
    public function getCommits()
    {
        return $this->commits;
    }

    /**
     * @return Commit
     */
    public function getLastCommit()
    {
        if (empty($this->commits)) {
            return null;
        }

        $iterator = $this->commits->getIterator();
        $iterator->uasort(function (Commit $first, Commit $second) {
            return $first->getCreatedAt() < $second->getCreatedAt() ? 1 : -1;
        });

        return $iterator->current();
    }
}
