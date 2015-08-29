<?php

namespace AppBundle\Worker;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class GarbageCollector
{
    /**
     * @var string
     */
    private $baseDir;

    /**
     * @param string $baseDir
     */
    public function __construct($baseDir = '/tmp')
    {
        $this->baseDir = $baseDir;
    }

    /**
     *
     */
    public function run()
    {
        $fs = new Filesystem();

        if (! $fs->exists($this->baseDir)) {
            return;
        }

        $finder = (new Finder())
            ->in($this->baseDir)
            ->directories()
            ->depth('== 0');

        foreach ($finder as $dir) {
            $fs->remove($dir->getRealpath());
        }
    }
}
