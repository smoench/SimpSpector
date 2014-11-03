<?php

namespace SimpleThings\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class MergeRequest
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
     * @ORM\Column(name="remoteId", type="string", length=255)
     */
    private $remoteId;

    /**
     * @var string
     *
     * @ORM\Column(name="sourceBranch", type="string", length=255)
     */
    private $sourceBranch;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="mergeRequests")
     */
    private $project;

    /**
     * @var Push[]
     *
     * @ORM\OneToMany(targetEntity="Push", mappedBy="mergeRequest")
     */
    private $pushes;

    /**
     *
     */
    public function __construct()
    {
        $this->mergeRequests = new ArrayCollection();
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
     * Set remoteId
     *
     * @param string $remoteId
     * @return MergeRequest
     */
    public function setRemoteId($remoteId)
    {
        $this->remoteId = $remoteId;

        return $this;
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
     * @return MergeRequest
     */
    public function setSourceBranch($sourceBranch)
    {
        $this->sourceBranch = $sourceBranch;

        return $this;
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
     * @return Push[]
     */
    public function getPushes()
    {
        return $this->pushes;
    }

    /**
     * @param Push[] $pushes
     */
    public function setPushes($pushes)
    {
        $this->pushes = $pushes;
    }
}
