<?php

namespace SimpSpector\Provider\Event;

/**
 * @author Simon Mönch <simonmoench@gmail.com>
 */
class PushEvent implements EventInterface
{
    /**
     * @var string
     */
    private $branch;

    /**
     * @var string
     */
    private $projectId;

    /**
     * @var string
     */
    private $revision;

    /**
     * @param string $branch
     * @param string $projectId
     * @param string $revision
     */
    public function __construct($branch, $projectId, $revision)
    {
        $this->branch    = $branch;
        $this->projectId = $projectId;
        $this->revision  = $revision;
    }

    /**
     * @return string
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @return string
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @return string
     */
    public function getRevision()
    {
        return $this->revision;
    }
}
