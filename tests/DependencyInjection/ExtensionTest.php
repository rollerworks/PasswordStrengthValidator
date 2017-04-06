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
use Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints\Blacklist;
use Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints\PasswordStrength;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\AddConstraintValidatorsPass as LegacyAddConstraintValidatorsPass;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\DependencyInjection\AddConstraintValidatorsPass;

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

        self::assertTrue($provider->isBlacklisted('foo'));
        self::assertTrue($provider->isBlacklisted('foobar'));
        self::assertTrue($provider->isBlacklisted('kaboom'));
        self::assertFalse($provider->isBlacklisted('leeRoy'));
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
        self::assertTrue($provider->isBlacklisted('foo'));
        self::assertTrue($provider->isBlacklisted('foobar'));
        self::assertTrue($provider->isBlacklisted('kaboom'));
        self::assertTrue($provider->isBlacklisted('doctor'));
        self::assertFalse($provider->isBlacklisted('leeRoy'));
    }

    public function testPasswordValidatorsAreRegistered()
    {
        if (class_exists('Symfony\Component\Validator\DependencyInjection\AddConstraintValidatorsPass')) {
            $this->container->addCompilerPass(new AddConstraintValidatorsPass());
        } else {
            $this->container->addCompilerPass(new LegacyAddConstraintValidatorsPass());
        }

        $this->container->register(
            'validator.validator_factory',
            'Symfony\Bundle\FrameworkBundle\Validator\ConstraintValidatorFactory'
        )->setArguments(array(new Reference('service_container'), array()));

        $this->load();
        $this->compile();

        /** @var ConstraintValidatorFactory $factory */
        $factory = $this->container->get('validator.validator_factory');

        self::assertInstanceOf('Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints\PasswordStrengthValidator', $factory->getInstance(new PasswordStrength(array('minStrength' => 1))));
        self::assertInstanceOf('Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints\BlacklistValidator', $factory->getInstance(new Blacklist()));
    }

    protected function getContainerExtensions()
    {
        return array(
            new RollerworksPasswordStrengthExtension(),
        );
    }
}
