<?php

namespace SimpleThings\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Project
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
     * @var MergeRequest[]
     *
     * @ORM\OneToMany(targetEntity="MergeRequest", mappedBy="project")
     */
    private $mergeRequests;

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
}
