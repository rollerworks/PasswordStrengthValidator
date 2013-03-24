<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rollerworks\Bundle\PasswordStrengthBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * {@inheritDoc}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
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
            ->end();

        return $treeBuilder;
    }
}
