<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectRepository")
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
    private $namespace;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $repositoryUrl;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
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
     * @var Branch[]
     *
     * @ORM\OneToMany(targetEntity="Branch", mappedBy="project")
     */
    private $branches;

    /**
     * @var Tag[]
     *
     * @ORM\OneToMany(targetEntity="Tag", mappedBy="project")
     */
    private $tags;

    /**
     * @var Commit[]
     *
     * @ORM\OneToMany(targetEntity="Commit", mappedBy="project")
     */
    private $commits;

    /**
     * @var NewsStreamItem[]
     *
     * @ORM\OneToMany(targetEntity="NewsStreamItem", mappedBy="project")
     */
    private $newsStreamItems;

    /**
     *
     */
    public function __construct()
    {
        $this->mergeRequests   = new ArrayCollection();
        $this->branches        = new ArrayCollection();
        $this->commits         = new ArrayCollection();
        $this->tags            = new ArrayCollection();
        $this->newsStreamItems = new ArrayCollection();
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
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
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
     * @return MergeRequest[]|ArrayCollection
     */
    public function getMergeRequests()
    {
        return $this->mergeRequests;
    }

    /**
     * @return Branch[]|ArrayCollection
     */
    public function getBranches()
    {
        return $this->branches;
    }

    /**
     * @return Tag[]|ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return Commit[]|ArrayCollection
     */
    public function getCommits()
    {
        return $this->commits;
    }

    /**
     * @return NewsStreamItem[]|ArrayCollection
     */
    public function getNewsStreamItems()
    {
        return $this->newsStreamItems;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        /* todo remove */
        if (!$this->namespace) {
            return $this->name;
        }

        return $this->namespace . '/' . $this->name;
    }
}
