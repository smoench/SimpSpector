<?php
/**
 * @author Lars Wallenborn <lars@wallenborn.net>
 */

namespace SimpleThings\AppBundle\Badge;

use SimpleThings\AppBundle\Entity\Commit;

class ScoreCalculator
{
    /**
     * @param Commit $commit
     * @return Score
     */
    public function get(Commit $commit)
    {
        switch ($commit->getStatus()) {
            case Commit::STATUS_SUCCESS:
                $result = $commit->getResult();
                $number = isset($result['phpcs']) && ! empty($result['phpcs']) ? 100 : 0;
                $score  = new Score($number, $this->getColor($number));
                break;
            case Commit::STATUS_ERROR:
                $score = new Score('-', 'FF0000');
                break;
            case Commit::STATUS_NEW:
            case Commit::STATUS_RUN:
            default:
                $score = new Score('...', 'CCCCCC');
                break;
        }


        return $score;
    }

    /**
     * @param int $n
     * @return string
     */
    private function getColor($n)
    {
        $n = 100 - $n;
        $r = (255 * $n) / 100;
        $g = (255 * (100 - $n)) / 100;
        $b = 0;

        return sprintf('%02X%02X%02X', $r, $g, $b);
    }
}
