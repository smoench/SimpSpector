<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SimpleThings\AppBundle\Repository\ProjectRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Project implements TimestampableInterface
{
    use Timestampable;

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
    private $repositoryUrl;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $webUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="remoteId", type="string", length=255)
     */
    private $remoteId;

    /**
     * @var MergeRequest[]
     *
     * @ORM\OneToMany(targetEntity="MergeRequest", mappedBy="project")
     */
    private $mergeRequests;

    /**
     * @var Commit[]
     *
     * @ORM\OneToMany(targetEntity="Commit", mappedBy="project")
     */
    private $commits;

    /**
     *
     */
    public function __construct()
    {
        $this->mergeRequests = new ArrayCollection();
        $this->commits       = new ArrayCollection();
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
    public function getRepositoryUrl()
    {
        return $this->repositoryUrl;
    }

    /**
     * @param string $repository
     */
    public function setRepositoryUrl($repository)
    {
        $this->repositoryUrl = $repository;
    }

    /**
     * @return string
     */
    public function getWebUrl()
    {
        return $this->webUrl;
    }

    /**
     * @param string $webUrl
     */
    public function setWebUrl($webUrl)
    {
        $this->webUrl = $webUrl;
    }

    /**
     * Set remoteId
     *
     * @param string $remoteId
     * @return Project
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
     * @return MergeRequest[]
     */
    public function getMergeRequests()
    {
        return $this->mergeRequests;
    }

    /**
     * @return Commit[]
     */
    public function getCommits()
    {
        return $this->commits;
    }
}
