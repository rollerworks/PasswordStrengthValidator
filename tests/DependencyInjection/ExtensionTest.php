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

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Rollerworks\Bundle\PasswordStrengthBundle\DependencyInjection\RollerworksPasswordStrengthExtension;
use Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints\Blacklist as BlacklistConstraint;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\AddConstraintValidatorsPass;
use Symfony\Component\Validator\Tests\Fixtures\Reference;

class ExtensionTest extends AbstractExtensionTestCase
{
    public function testLoadDefaultConfiguration()
    {
        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasService('rollerworks_password_strength.blacklist.validator');
        $this->assertContainerBuilderHasService(
            'rollerworks_password_strength.blacklist_provider',
            'Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\NoopProvider'
        );

        $constraint = new BlacklistConstraint();
        $this->assertContainerBuilderHasService($constraint->validatedBy());
    }

    public function testLoadWithSqliteConfiguration()
    {
        $this->load(array(
            'blacklist' => array(
                'default_provider' => 'rollerworks_password_strength.blacklist.provider.sqlite',
                'providers' => array(
                    'sqlite' => array('dsn' => 'sqlite:something'),
                ),
            ),
        ));

        $this->compile();

        $this->assertContainerBuilderHasService('rollerworks_password_strength.blacklist.validator');
        $this->assertContainerBuilderHasService(
            'rollerworks_password_strength.blacklist_provider',
            'Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\SqliteProvider'
        );
    }

    public function testLoadWithArrayConfiguration()
    {
        $this->load(array(
            'blacklist' => array(
                'default_provider' => 'rollerworks_password_strength.blacklist.provider.array',
                'providers' => array(
                    'array' => array('foo', 'foobar', 'kaboom'),
                ),
            ),
        ));

        $this->compile();

        $this->assertContainerBuilderHasService('rollerworks_password_strength.blacklist.validator');
        $this->assertContainerBuilderHasService(
            'rollerworks_password_strength.blacklist_provider',
            'Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\ArrayProvider'
        );

        $provider = $this->container->get('rollerworks_password_strength.blacklist_provider');

        $this->assertTrue($provider->isBlacklisted('foo'));
        $this->assertTrue($provider->isBlacklisted('foobar'));
        $this->assertTrue($provider->isBlacklisted('kaboom'));
        $this->assertFalse($provider->isBlacklisted('leeRoy'));
    }

    public function testLoadWithChainConfiguration()
    {
        $this->load(array(
            'blacklist' => array(
                'default_provider' => 'rollerworks_password_strength.blacklist.provider.chain',
                'providers' => array(
                    'array' => array('foo', 'foobar', 'kaboom'),
                    'chain' => array(
                        'providers' => array(
                            'rollerworks_password_strength.blacklist.provider.array',
                            'acme.password_blacklist.array',
                        ),
                    ),
                ),
            ),
        ));

        $this->container->set(
            'acme.password_blacklist.array',
            new \Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\ArrayProvider(array('amy', 'doctor', 'rory'))
        );

        $this->compile();

        $this->assertContainerBuilderHasService('rollerworks_password_strength.blacklist.validator');
        $this->assertContainerBuilderHasService(
            'rollerworks_password_strength.blacklist_provider',
            'Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\ChainProvider'
        );

        $provider = $this->container->get('rollerworks_password_strength.blacklist_provider');
        $this->assertTrue($provider->isBlacklisted('foo'));
        $this->assertTrue($provider->isBlacklisted('foobar'));
        $this->assertTrue($provider->isBlacklisted('kaboom'));
        $this->assertTrue($provider->isBlacklisted('doctor'));
        $this->assertFalse($provider->isBlacklisted('leeRoy'));
    }

    public function testPasswordValidatorsAreRegistered()
    {
        $this->container->addCompilerPass(new AddConstraintValidatorsPass());
        $this->container->register(
            'validator.validator_factory',
            'Symfony\Bundle\FrameworkBundle\Validator\ConstraintValidatorFactory'
        )->setArguments(array(new Reference('service_container'), array()));

        $this->load();
        $this->compile();

        $validatorFactory = $this->container->getDefinition('validator.validator_factory');
        $factoryArguments = $validatorFactory->getArguments();

        $validators = array_values($factoryArguments[1]);

        // Use only the service-id as the alias is considered deprecated.
        // https://github.com/symfony/symfony/issues/16805
        $this->assertContains('rollerworks_password_strength.validator.password_strength', $validators);
        $this->assertContains('rollerworks_password_strength.blacklist.validator', $validators);
    }

    protected function getContainerExtensions()
    {
        return array(
            new RollerworksPasswordStrengthExtension(),
        );
    }
}
