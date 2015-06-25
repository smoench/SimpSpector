<?php

namespace AppBundle\Logger;

use AppBundle\Entity\Commit;
use Symfony\Component\Filesystem\Filesystem;

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
        $this->path = rtrim($path, '/');

        $fs = new Filesystem();
        $fs->mkdir($this->path, 0770);
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