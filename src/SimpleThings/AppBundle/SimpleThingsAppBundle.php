<?php

namespace SimpleThings\AppBundle;

use SimpSpector\Analyser\DependencyInjection\ContainerConfigurator;
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
        $container->setParameter('simpspector.analyser.bin', __DIR__ . '/../../../bin/');
        (new ContainerConfigurator())->prepare($container);
    }
}
