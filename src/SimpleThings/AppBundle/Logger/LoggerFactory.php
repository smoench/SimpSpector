<?php
/**
 * (c) SimpleThings GmbH
 */

namespace SimpleThings\AppBundle\Logger;

use SimpleThings\AppBundle\Entity\Commit;
use SimpleThings\AppBundle\Util;

/**
 * @author David Badura <badura@simplethings.de>
 */
class LoggerFactory
{
    /**
     * @var string
     */
    private $path;

    /**
     * @param string $path
     */
    public function __construct($path = '/tmp')
    {
        $this->path = $path;
    }

    /**
     * @param Commit $commit
     * @return FileLogger
     */
    public function createLogger(Commit $commit)
    {
        $fileName = Util::getUniqueIdByCommit($commit) . '.log';

        return new FileLogger($this->path . '/' . $fileName);
    }
}