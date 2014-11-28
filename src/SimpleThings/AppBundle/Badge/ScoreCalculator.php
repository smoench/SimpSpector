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
        return new Score(70, $this->getColor(70));
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
