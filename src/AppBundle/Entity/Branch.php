<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BranchRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Branch implements TimestampableInterface
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
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="mergeRequests", cascade={"all"})
     */
    private $project;

    /**
     * @var Commit[]
     *
     * @ORM\OneToMany(targetEntity="Commit", mappedBy="mergeRequest")
     */
    private $commits;

    /**
     *
     */
    public function __construct()
    {
        $this->commits = new ArrayCollection();
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
