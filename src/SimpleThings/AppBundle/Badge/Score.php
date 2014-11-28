<?php
/**
 * @author Lars Wallenborn <lars@wallenborn.net>
 */

namespace SimpleThings\AppBundle\Badge;

class Score
{
    public $number;
    public $color;

    /**
     * @param int $number
     * @param string $color
     */
    function __construct($number, $color)
    {
        $this->number = $number;
        $this->color  = $color;
    }
}
