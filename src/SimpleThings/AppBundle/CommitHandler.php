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
     * @var GadgetExecutor
     */
    private $gadgetExecutor;

    /**
     * @param GitCheckout $gitCheckout
     * @param GadgetExecutor $gadgetExecutor
     * @param ConfigLoader $loader
     */
    public function __construct(GitCheckout $gitCheckout, ConfigLoader $loader, GadgetExecutor $gadgetExecutor)
    {
        $this->gitCheckout = $gitCheckout;
        $this->gadgetExecutor = $gadgetExecutor;
        $this->configLoader = $loader;
    }

    /**
     * @param Commit $commit
     */
    public function handle(Commit $commit)
    {
        $workspace = $this->gitCheckout->create($commit);
        $workspace->config = $this->configLoader->load($workspace);

        $commit->setResult(
            $this->gadgetExecutor->run($workspace)
        );

        //$this->gitCheckout->remove($workspace);
    }    
} 
