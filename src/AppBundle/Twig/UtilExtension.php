<?php

namespace AppBundle\Twig;

use SimpleThings\AppBundle\Entity\Commit;
use SimpleThings\AppBundle\Score\CalculatorInterface;
use SimpSpector\Analyser\Issue;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class UtilExtension extends \Twig_Extension
{
    /**
     * @var CalculatorInterface
     */
    private $calculator;

    /**
     * @param CalculatorInterface $calculator
     */
    public function __construct(CalculatorInterface $calculator)
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
            'score'        => new \Twig_SimpleFunction('score', [$this, 'score']),
            'score_color'  => new \Twig_SimpleFunction('score_color', [$this, 'scoreColor'])
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
     * @return array
     */
    public function score(Commit $commit)
    {
        switch ($commit->getStatus()) {
            case Commit::STATUS_SUCCESS:
                $number = $this->calculator->calculate($commit->getResult());
                $score  = ['number' => $number, 'color' => $this->scoreColor($number)];
                break;
            case Commit::STATUS_ERROR:
                $score = ['number' => '-', 'color' => 'FF0000'];
                break;
            case Commit::STATUS_NEW:
            case Commit::STATUS_RUN:
            default:
                $score = ['number' => '...', 'color' => 'CCCCCC'];
                break;
        }

        return $score;
    }

    /**
     * @param int $number
     * @return string
     */
    public function scoreColor($number)
    {
        $number = 100 - $number;
        $r      = (255 * $number) / 100;
        $g      = (255 * (100 - $number)) / 100;
        $b      = 0;

        return sprintf('%02X%02X%02X', $r, $g, $b);
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
