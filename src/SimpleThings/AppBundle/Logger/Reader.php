<?php
/**
 * (c) SimpleThings GmbH
 */

namespace SimpleThings\AppBundle\Logger;

use SimpleThings\AppBundle\Entity\Commit;

/**
 * @author David Badura <badura@simplethings.de>
 */
class Reader
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
     * @return string
     */
    public function getContent(Commit $commit)
    {
        $file = $this->locator->getLogFilePath($commit);

        if (file_exists($file)) {
            return file_get_contents($file);
        }

        return 'no log file found';
    }
}