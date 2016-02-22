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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * {@inheritdoc}
 */
class RollerworksPasswordStrengthExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('rollerworks_password_strength.blacklist_provider', $config['blacklist']['default_provider']);
        $container->setParameter('rollerworks_password_strength.blacklist.sqlite.dsn', '');

        $container->setAlias('rollerworks_password_strength.blacklist_provider', $config['blacklist']['default_provider']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('strength_validator.xml');
        $loader->load('blacklist.xml');

        if (isset($config['blacklist']['providers'])) {
            $this->setBlackListProvidersConfiguration($config['blacklist']['providers'], $container);
        }
    }

    private function setBlackListProvidersConfiguration(array $config, ContainerBuilder $container)
    {
        if (isset($config['sqlite'])) {
            $container->setParameter('rollerworks_password_strength.blacklist.sqlite.dsn', $config['sqlite']['dsn']);
            $container->getDefinition('rollerworks_password_strength.blacklist.provider.sqlite')->setPublic(true);
        }

        if (isset($config['array'])) {
            $container
                ->getDefinition('rollerworks_password_strength.blacklist.provider.array')
                ->replaceArgument(0, $config['array']);
        }

        if (isset($config['chain'])) {
            $chainLoader = $container->getDefinition('rollerworks_password_strength.blacklist.provider.chain');

            foreach ($config['chain']['providers'] as $provider) {
                $chainLoader->addMethodCall('addProvider', array(new Reference($provider)));
            }
        }
    }
}
