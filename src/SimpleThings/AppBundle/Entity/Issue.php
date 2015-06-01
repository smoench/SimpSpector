<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SimpSpector\Analyser\Issue as AnalyserIssue;

/**
 * @author David Badura <d.a.badura@gmail.com>
 *
 * @ORM\Table()
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Issue implements TimestampableInterface
{
    use Timestampable;

    const LEVEL_NOTICE   = 'notice';
    const LEVEL_WARNING  = 'warning';
    const LEVEL_ERROR    = 'error';
    const LEVEL_CRITICAL = 'critical';

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $gadget;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $level;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $file;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $line;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    private $extraInformation;

    /**
     * @var Commit
     *
     * @ORM\ManyToOne(targetEntity="SimpleThings\AppBundle\Entity\Commit", inversedBy="issues")
     */
    private $commit;

    /**
     * @param string $title
     * @param string $gadget
     * @param string $level
     */
    public function __construct($title, $gadget = 'simpspector', $level = self::LEVEL_NOTICE)
    {
        $this->title            = $title;
        $this->gadget           = $gadget;
        $this->level            = $level;
        $this->extraInformation = [];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getGadget()
    {
        return $this->gadget;
    }

    /**
     * @param string $gadget
     */
    public function setGadget($gadget)
    {
        $this->gadget = $gadget;
    }

    /**
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param string $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return null|string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param null|string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return int|null
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param int|null $line
     */
    public function setLine($line)
    {
        $this->line = $line;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return array
     */
    public function getExtraInformation()
    {
        return $this->extraInformation;
    }

    /**
     * @param array $extraInformation
     */
    public function setExtraInformation($extraInformation)
    {
        $this->extraInformation = $extraInformation;
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
     * @param AnalyserIssue $issue
     * @return Issue
     */
    public static function createFromAnalyser(AnalyserIssue $issue)
    {
        $obj = new self($issue->getTitle(), $issue->getGadget(), $issue->getLevel());
        $obj->setDescription($issue->getDescription());
        $obj->setFile($issue->getFile());
        $obj->setLine($issue->getLine());
        $obj->setExtraInformation($issue->getExtraInformation());

        return $obj;
    }
}