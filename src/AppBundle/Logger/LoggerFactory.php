<?php
/**
 * (c) SimpleThings GmbH
 */

namespace AppBundle\Logger;

use SimpleThings\AppBundle\Entity\Commit;
use SimpSpector\Analyser\Logger\FileLogger;

/**
 * @author David Badura <badura@simplethings.de>
 */
class LoggerFactory
{
    /**
     * @var FileLocator
     */
    private $locator;

    /**
     * @param FileLocator $locator
     */
    public function __construct(FileLocator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * @param Commit $commit
     * @return FileLogger
     */
    public function createLogger(Commit $commit)
    {
        $file = $this->locator->getLogFilePath($commit);

        if (file_exists($file)) {
            unlink($file);
        }

        return new FileLogger($file);
    }
}