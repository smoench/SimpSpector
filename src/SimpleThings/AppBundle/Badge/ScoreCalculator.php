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
        $result = $commit->getResult();
        $number = isset($result['phpcs']) && ! empty($result['phpcs']) ? 100 : 0;

        return new Score($number, $this->getColor($number));
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
