<?php

namespace AppBundle\DependencyInjection;

use AppBundle\Provider\GithubProvider;
use AppBundle\Provider\GitlabProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class AppExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('repositories.xml');
        $loader->load('webhooks.xml');
        
        $providers = [
            'gitlab' => GitlabProvider::class,
            'github' => GithubProvider::class
        ];

        if (!isset($providers[$container->getParameter('provider_type')])) {
            throw new \Exception('provider not supported');
        }

        $container->register('simpspector.provider', $providers[$container->getParameter('provider_type')])
            ->addArgument($container->getParameter('provider_url'))
            ->addArgument($container->getParameter('provider_token'));
    }
}
