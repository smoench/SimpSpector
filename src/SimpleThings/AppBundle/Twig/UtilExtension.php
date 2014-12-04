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
            'level_color' => new \Twig_SimpleFilter('level_color', [$this, 'colorLevel']),
            'level_icon'  => new \Twig_SimpleFilter('level_icon', [$this, 'iconLevel'])
        ];
    }

    /**
     * @param Issue[] $issues
     * @return array
     */
    public function groupIssues($issues)
    {
        $gadgets = [];
        $levels  = [
            Issue::LEVEL_NOTICE   => [],
            Issue::LEVEL_WARNING  => [],
            Issue::LEVEL_ERROR    => [],
            Issue::LEVEL_CRITICAL => []
        ];

        foreach ($issues as $issue) {
            $gadgets[$issue->getGadget()][] = $issue;
            $levels[$issue->getLevel()][]   = $issue;
        }

        ksort($gadgets);

        $levels = array_filter($levels, function ($array) {
            return !empty($array);
        });

        return [
            'gadget' => $gadgets,
            'level'  => $levels
        ];
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
     * @param Issue $issue
     * @return string
     */
    public function iconLevel(Issue $issue)
    {
        switch ($issue->getLevel()) {
            case Issue::LEVEL_NOTICE:
                return 'info';
            case Issue::LEVEL_WARNING:
                return 'warning';
            case Issue::LEVEL_ERROR:
                return 'bug';
            case Issue::LEVEL_CRITICAL:
                return 'fire';
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
