<?php
/**
 * (c) SimpleThings GmbH
 */

namespace SimpleThings\AppBundle\Logger;

use SimpleThings\AppBundle\Entity\Commit;

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
        $fileName = $commit->getUniqueId() . '.log';
        $file = $this->path . '/' . $fileName;

        if (file_exists($file)) {
            unlink($file);
        }

        return new FileLogger($file);
    }
}