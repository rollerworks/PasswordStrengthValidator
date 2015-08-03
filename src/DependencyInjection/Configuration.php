<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\PasswordStrengthBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * {@inheritdoc}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('rollerworks_password_strength');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('blacklist')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default_provider')->defaultValue('rollerworks_password_strength.blacklist.provider.noop')->end()
                        ->arrayNode('providers')
                            ->fixXmlConfig('provider')
                            ->children()
                                ->arrayNode('sqlite')
                                    ->children()
                                        ->scalarNode('dsn')->defaultNull()->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                                ->arrayNode('chain')
                                    ->children()
                                        ->arrayNode('providers')
                                            ->fixXmlConfig('provider')
                                            ->prototype('scalar')->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('array')->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
