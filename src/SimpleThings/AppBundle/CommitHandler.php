<?php

namespace SimpleThings\AppBundle;

use SimpleThings\AppBundle\Entity\Commit;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class CommitHandler
{
    /**
     * @var GitCheckout
     */
    private $gitCheckout;

    /**
     * @var ConfigLoader
     */
    private $configLoader;

    /**
     * @param GitCheckout $gitCheckout
     * @param ConfigLoader $loader
     */
    public function __construct(GitCheckout $gitCheckout, ConfigLoader $loader)
    {
        $this->gitCheckout = $gitCheckout;
        $this->configLoader = $loader;
    }

    /**
     * @param Commit $commit
     */
    public function handle(Commit $commit)
    {
        $workingCopy = $this->gitCheckout->create($commit);
        $workingCopy->config = $this->configLoader->load($workingCopy);

        $this->gitCheckout->remove($workingCopy);
    }
} 