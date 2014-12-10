<?php
/**
 * (c) SimpleThings GmbH
 */

namespace SimpleThings\AppBundle\Logger;

/**
 * @author David Badura <badura@simplethings.de>
 */
abstract class AbstractLogger
{
    /**
     * @param string $message
     */
    public function writeln($message)
    {
        $this->write($message . "\n");
    }

    /**
     * @param string $message
     */
    abstract public function write($message);
} 