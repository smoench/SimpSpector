<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Twig;

use SimpleThings\AppBundle\Badge\Score;
use SimpleThings\AppBundle\Badge\ScoreCalculator;
use SimpleThings\AppBundle\Entity\Commit;
use SimpleThings\AppBundle\Entity\Issue;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class UtilExtension extends \Twig_Extension
{
    /**
     * @var ScoreCalculator
     */
    private $calculator;

    /**
     * @param ScoreCalculator $calculator
     */
    public function __construct(ScoreCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            'group_issues' => new \Twig_SimpleFunction('group_issues', [$this, 'groupIssues']),
            'score'        => new \Twig_SimpleFunction('score', [$this, 'score'])
        ];
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            'level_color' => new \Twig_SimpleFilter('level_color', [$this, 'colorLevel'])
        ];
    }

    /**
     * @param Issue[] $issues
     * @return array
     */
    public function groupIssues($issues)
    {
        $groups = [
            'gadget' => [],
            'level'  => []
        ];

        foreach ($issues as $issue) {
            $groups['gadget'][$issue->getGadget()][] = $issue;
            $groups['level'][$issue->getLevel()][]   = $issue;
        }

        ksort($groups['gadget']);
        ksort($groups['level']);

        return $groups;
    }

    /**
     * @param Issue $issue
     * @return string
     */
    public function colorLevel(Issue $issue)
    {
        switch ($issue->getLevel()) {
            case Issue::LEVEL_NOTICE:
                return 'blue';
            case Issue::LEVEL_WARNING:
                return 'yellow';
            case Issue::LEVEL_ERROR:
                return 'orange';
            case Issue::LEVEL_CRITICAL:
                return 'red';
        }
    }

    /**
     * @param Commit $commit
     * @return Score
     */
    public function score(Commit $commit)
    {
        return $this->calculator->get($commit);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'issue';
    }
}
