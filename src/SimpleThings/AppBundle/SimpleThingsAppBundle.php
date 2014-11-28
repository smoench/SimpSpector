<?php

namespace SimpleThings\AppBundle;

use SimpleThings\AppBundle\DependencyInjection\Compiler\GadgetPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class SimpleThingsAppBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new GadgetPass());
    }
}
