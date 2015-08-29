<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TagRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Tag implements TimestampableInterface
{
    use Timestampable;

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
    private $name;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="mergeRequests", cascade={"all"})
     */
    private $project;

    /**
     * @var Commit
     *
     * @ORM\ManyToOne(targetEntity="Commit")
     */
    private $commit;

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
     * @return Commit
     */
    public function getCommit()
    {
        return $this->commit;
    }

    /**
     * @param Commit $commit
     */
    public function setCommit($commit)
    {
        $this->commit = $commit;
    }
}
