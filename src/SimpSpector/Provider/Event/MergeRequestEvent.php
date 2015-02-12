<?php

namespace SimpSpector\Provider\Event;

/**
 * @author Simon Mönch <simonmoench@gmail.com>
 */
class MergeRequestEvent implements EventInterface
{
    /**
     * @var string
     */
    private $projectId;

    /**
     * @var string
     */
    private $mergeId;

    /**
     * @var string
     */
    private $branch;

    /**
     * @param string $projectId
     * @param string $mergeId
     * @param string $branch
     */
    public function __construct($projectId, $mergeId, $branch)
    {
        $this->projectId = $projectId;
        $this->mergeId   = $mergeId;
        $this->branch    = $branch;
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
    public function getMergeId()
    {
        return $this->mergeId;
    }

    /**
     * @return string
     */
    public function getBranch()
    {
        return $this->branch;
    }
}