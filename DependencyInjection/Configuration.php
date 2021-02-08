<?php

namespace Vibbe\ACL\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('vibbe_acl_bundle');

        $treeBuilder
            ->getRootNode()
                ->children()
                ->variableNode('vibbe_acl_actions')
                    ->end()
                ->end()
            ->end();


        return $treeBuilder;
    }

}
