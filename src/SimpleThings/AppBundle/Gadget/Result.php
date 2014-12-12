<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Gadget;

use SimpleThings\AppBundle\Entity\Issue;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Result
{
    /**
     * @var Issue[]
     */
    private $issues = [];

    /**
     * @param Issue $issue
     */
    public function addIssue(Issue $issue)
    {
        $this->issues[] = $issue;
    }

    /**
     * @return Issue[]
     */
    public function getIssues()
    {
        return $this->issues;
    }

    /**
     * @param Result $result
     */
    public function merge(Result $result)
    {
        $this->issues = array_merge($this->issues, $result->getIssues());
    }
}