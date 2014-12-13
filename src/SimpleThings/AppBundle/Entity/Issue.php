<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author David Badura <d.a.badura@gmail.com>
 *
 * @ORM\Table()
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Issue
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
    private $message;

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
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $codeSnippet;

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
     * @param string $message
     * @param string $gadget
     * @param string $level
     */
    public function __construct($message, $gadget = 'simpspector', $level = self::LEVEL_NOTICE)
    {
        $this->message          = $message;
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
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
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
    public function getCodeSnippet()
    {
        return $this->codeSnippet;
    }

    /**
     * @param string $codeSnippet
     */
    public function setCodeSnippet($codeSnippet)
    {
        $this->codeSnippet = $codeSnippet;
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
}