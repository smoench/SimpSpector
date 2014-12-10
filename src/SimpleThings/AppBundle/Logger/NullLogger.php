<?php
/**
 * (c) SimpleThings GmbH
 */

namespace SimpleThings\AppBundle\Logger;

/**
 * @author David Badura <badura@simplethings.de>
 */
class NullLogger extends AbstractLogger
{
    /**
     * @param $message
     */
    public function write($message)
    {
        // do nothing
    }
}