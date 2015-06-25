<?php

namespace AppBundle\Score;

use SimpSpector\Analyser\Result;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
interface CalculatorInterface
{
    /**
     * @param Result $result
     * @return float
     */
    public function calculate(Result $result);
}
