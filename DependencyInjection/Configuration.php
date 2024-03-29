<?php

namespace MandarinMedien\MMCmfContentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('mm_cmf_content');
        $rootNode = $treeBuilder->getRootNode();

        $this->addContentNodesSection($rootNode);
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }

    private function addContentNodesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('page_nodes')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('icon')->end()
                            ->arrayNode('simpleForm')
                                ->children()
                                    ->scalarNode('type')->end()
                                    ->scalarNode('template')->end()
                                ->end()
                            ->end()
                            ->arrayNode('hiddenFields')
                                ->prototype('scalar')
                                ->end()
                            ->end()
                            ->arrayNode('templates')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('name')->end()
                                        ->scalarNode('template')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('content_nodes')
                    ->prototype('array')
                        ->children()
                            ->booleanNode('gridable')
                                ->defaultTrue()
                            ->end()
                            ->scalarNode('icon')->end()
                            ->arrayNode('simpleForm')
                                ->children()
                                    ->scalarNode('type')->end()
                                    ->scalarNode('template')->end()
                                ->end()
                            ->end()
                            ->arrayNode('hiddenFields')
                                ->prototype('scalar')
                                ->end()
                            ->end()
                            ->arrayNode('templates')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('name')->end()
                                        ->scalarNode('template')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
