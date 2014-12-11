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
class Reader
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
     * @return string
     */
    public function getContent(Commit $commit)
    {
        $fileName = Util::getUniqueIdByCommit($commit) . '.log';

        $file = $this->path . '/' . $fileName;

        if (file_exists($file)) {
            return file_get_contents($file);
        }

        return 'no log file found';
    }
}