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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * {@inheritDoc}
 */
class RollerworksPasswordStrengthExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $container->setAlias('rollerworks_password_strength.blacklist_provider');

        if (isset($config['blacklist']['providers']['sqlite'])) {
           $container->setParameter('rollerworks_password_strength.blacklist.sqlite.dsn', $config['blacklist']['providers']['sqlite']['dsn']);
        }

        if (isset($config['blacklist']['providers']['chain'])) {
            $chainLoader = $container->getDefinition('rollerworks_password_strength.blacklist.provider.chain');

            foreach ($config['blacklist']['providers']['chain']['providers'] as $provider) {
                $chainLoader->addMethodCall('addProvider', array(new Reference($provider)));
            }
        }

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('blacklist.xml');
    }
}
