<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Logger;

use SimpleThings\AppBundle\Entity\Commit;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class FileLocator
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
    public function getLogFilePath(Commit $commit)
    {
        $fileName = $commit->getUniqueId() . '.log';

        return $this->path . '/' . $fileName;
    }
}