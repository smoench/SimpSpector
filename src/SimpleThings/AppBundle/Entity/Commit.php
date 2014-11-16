<?php

namespace SimpleThings\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Commit
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
     * @ORM\Column(name="revision", type="string", length=255)
     */
    private $revision;

    /**
     * @var array
     *
     * @ORM\Column(name="result", type="json_array", nullable=true)
     */
    private $result;

    /**
     * @var MergeRequest
     *
     * @ORM\ManyToOne(targetEntity="MergeRequest", inversedBy="commits", cascade={"all"})
     */
    private $mergeRequest;

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
     * Set result
     *
     * @param array $result
     */
    public function setResult(array $result)
    {
        $this->result = $result;
    }

    /**
     * Get result
     *
     * @return array
     */
    public function getResult()
    {
        return $this->result;
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
    public function setMergeRequest(MergeRequest $mergeRequest)
    {
        $this->mergeRequest = $mergeRequest;
    }
}
