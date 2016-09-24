<?php

namespace AppBundle\Entity;

use JMS\Serializer\Annotation as Serializer;
use SimpSpector\Analyser\Issue as BaseIssue;
use Symfony\Component\Serializer\Annotation as JSON;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Issue extends BaseIssue
{
    /**
     * @return string
     *
     * @JSON\Groups({"commit_full"})
     */
    public function getTitle()
    {
        return parent::getTitle();
    }

    /**
     * @return string
     *
     * @JSON\Groups({"commit_full"})
     */
    public function getGadget()
    {
        return parent::getGadget();
    }

    /**
     * @return string
     *
     * @JSON\Groups({"commit_full"})
     */
    public function getLevel()
    {
        return parent::getLevel();
    }

    /**
     * @return null|string
     *
     * @JSON\Groups({"commit_full"})
     */
    public function getFile()
    {
        return parent::getFile();
    }

    /**
     * @return int|null
     *
     * @JSON\Groups({"commit_full"})
     */
    public function getLine()
    {
        return parent::getLine();
    }

    /**
     * @return string
     *
     * @JSON\Groups({"commit_full"})
     */
    public function getDescription()
    {
        return parent::getDescription();
    }

    /**
     * @return array
     *
     * @JSON\Groups({"commit_full"})
     */
    public function getExtraInformation()
    {
        return parent::getExtraInformation();
    }
}
