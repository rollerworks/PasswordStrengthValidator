<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\PasswordStrengthBundle\Tests\DependencyInjection;

use Rollerworks\Bundle\PasswordStrengthBundle\DependencyInjection\RollerworksPasswordStrengthExtension;
use Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints\Blacklist as BlacklistConstraint;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadDefaultConfiguration()
    {
        $container = $this->createContainer();
        $container->registerExtension(new RollerworksPasswordStrengthExtension());
        $container->loadFromExtension('rollerworks_password_strength', array());
        $this->compileContainer($container);

        $this->assertTrue($container->has('rollerworks_password_strength.blacklist_provider'));
        $this->assertEquals('Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\NoopProvider', get_class($container->get('rollerworks_password_strength.blacklist_provider')));

        $constraint = new BlacklistConstraint();
        $this->assertTrue($container->has($constraint->validatedBy()));

        // This needs a proper test-case
        $container->get('rollerworks_password_strength.blacklist.validator');
    }

    public function testLoadWithSqliteConfiguration()
    {
        $container = $this->createContainer();
        $container->registerExtension(new RollerworksPasswordStrengthExtension());
        $container->loadFromExtension('rollerworks_password_strength', array(
            'blacklist' => array(
                'default_provider' => 'rollerworks_password_strength.blacklist.provider.sqlite',
                'providers' => array(
                    'sqlite' => array('dsn' => 'sqlite:something'),
                ),
            ),
        ));

        $this->compileContainer($container);

        $this->assertTrue($container->has('rollerworks_password_strength.blacklist_provider'));
        $this->assertEquals('Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\SqliteProvider', get_class($container->get('rollerworks_password_strength.blacklist_provider')));
    }

    public function testLoadWithArrayConfiguration()
    {
        $container = $this->createContainer();
        $container->registerExtension(new RollerworksPasswordStrengthExtension());
        $container->loadFromExtension('rollerworks_password_strength', array(
            'blacklist' => array(
                'default_provider' => 'rollerworks_password_strength.blacklist.provider.array',
                'providers' => array(
                    'array' => array('foo', 'foobar', 'kaboom'),
                ),
            ),
        ));

        $this->compileContainer($container);

        $this->assertTrue($container->has('rollerworks_password_strength.blacklist_provider'));
        $this->assertEquals('Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\ArrayProvider', get_class($container->get('rollerworks_password_strength.blacklist_provider')));

        $provider = $container->get('rollerworks_password_strength.blacklist_provider');
        $this->assertTrue($provider->isBlacklisted('foo'));
        $this->assertTrue($provider->isBlacklisted('foobar'));
        $this->assertTrue($provider->isBlacklisted('kaboom'));
        $this->assertFalse($provider->isBlacklisted('leeRoy'));
    }

    public function testLoadWithChainConfiguration()
    {
        $container = $this->createContainer();
        $container->registerExtension(new RollerworksPasswordStrengthExtension());
        $container->loadFromExtension('rollerworks_password_strength', array(
            'blacklist' => array(
                'default_provider' => 'rollerworks_password_strength.blacklist.provider.chain',
                'providers' => array(
                    'array' => array('foo', 'foobar', 'kaboom'),
                    'chain' => array('providers' => array('rollerworks_password_strength.blacklist.provider.array', 'acme.password_blacklist.array')),
                ),
            ),
        ));

        $container->set('acme.password_blacklist.array', new \Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\ArrayProvider(array('amy', 'doctor', 'rory')));
        $this->compileContainer($container);

        $this->assertTrue($container->has('rollerworks_password_strength.blacklist_provider'));
        $this->assertEquals('Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\ChainProvider', get_class($container->get('rollerworks_password_strength.blacklist_provider')));

        $provider = $container->get('rollerworks_password_strength.blacklist_provider');
        $this->assertTrue($provider->isBlacklisted('foo'));
        $this->assertTrue($provider->isBlacklisted('foobar'));
        $this->assertTrue($provider->isBlacklisted('kaboom'));
        $this->assertTrue($provider->isBlacklisted('doctor'));
        $this->assertFalse($provider->isBlacklisted('leeRoy'));
    }

    /**
     * @return ContainerBuilder
     */
    protected function createContainer()
    {
        $container = new ContainerBuilder(new ParameterBag(array(
            'kernel.cache_dir' => __DIR__.'/.cache',
            'kernel.charset' => 'UTF-8',
            'kernel.debug' => false,
        )));

        $container->set('service_container', $container);

        return $container;
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function compileContainer(ContainerBuilder $container)
    {
        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();
    }
}
