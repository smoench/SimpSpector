<?php

namespace AppBundle\Score;

use SimpSpector\Analyser\Result;

/**
 * @author Lars Wallenborn <lars@wallenborn.net>
 */
class Calculator implements CalculatorInterface
{
    /**
     * @param Result $result
     * @return float
     */
    public function calculate(Result $result)
    {
        return max(0, 100 - count($result->getIssues()));
    }
}
