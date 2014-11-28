<?php
/**
 *
 */

namespace SimpleThings\AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class GadgetPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('simple_things_app.gadget.repository')) {
            return;
        }

        foreach ($container->findTaggedServiceIds('simpspector.gadget') as $id => $attributes) {
            $container->getDefinition('simple_things_app.gadget.repository')->addMethodCall(
                'add',
                [new Reference($id)]
            );
        }
    }
}