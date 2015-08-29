<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NewsStreamItemRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class NewsStreamItem implements TimestampableInterface
{
    const TYPE_MERGE_REQUEST = 'merge_request';
    const TYPE_BRANCH        = 'branch';
    const TYPE_TAG           = 'tag';

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
    private $type;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project")
     */
    private $project;

    /**
     * @var Commit
     *
     * @ORM\ManyToOne(targetEntity="Commit")
     */
    private $commit;

    /**
     * @var MergeRequest
     *
     * @ORM\ManyToOne(targetEntity="MergeRequest")
     */
    private $mergeRequest;

    /**
     * @var Branch
     *
     * @ORM\ManyToOne(targetEntity="Branch")
     */
    private $branch;

    /**
     * @var Tag
     *
     * @ORM\ManyToOne(targetEntity="Tag")
     */
    private $tag;

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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
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

    /**
     * @return Branch
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param Branch $branch
     */
    public function setBranch(Branch $branch)
    {
        $this->branch = $branch;
    }

    /**
     * @return Tag
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param Tag $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }
}
