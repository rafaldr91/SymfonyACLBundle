<?php

namespace Vibbe\ACL\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class VibbeACLBundleExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration,$configs);

        $container->setParameter('vibbe_acl_bundle.vibbe_acl_actions', $config['vibbe_acl_actions'] ?? []);

        $loader->load('services.yaml');
    }

}
