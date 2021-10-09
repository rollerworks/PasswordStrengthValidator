<?php

/*
 * This file is part of the RollerworksPasswordStrengthValidator package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\PasswordStrength\Tests\Blacklist;

use PHPUnit\Framework\TestCase;
use Rollerworks\Component\PasswordStrength\Blacklist\ArrayProvider;
use Rollerworks\Component\PasswordStrength\Blacklist\ChainProvider;

/**
 * @internal
 * @group legacy
 */
final class ChainProviderTest extends TestCase
{
    /**
     * @test
     */
    public function black_list()
    {
        $provider = new ChainProvider();
        $provider->addProvider(new ArrayProvider(['test', 'foobar', 0]));
        $provider->addProvider(new ArrayProvider(['weak', 'god']));

        self::assertTrue($provider->isBlacklisted('test'));
        self::assertTrue($provider->isBlacklisted('foobar'));
        self::assertTrue($provider->isBlacklisted(0));

        self::assertTrue($provider->isBlacklisted('weak'));
        self::assertTrue($provider->isBlacklisted('god'));

        self::assertFalse($provider->isBlacklisted('tests'));
        self::assertFalse($provider->isBlacklisted(null));
        self::assertFalse($provider->isBlacklisted(false));
    }

    /**
     * @test
     */
    public function providers_by_construct()
    {
        $provider1 = new ArrayProvider(['test', 'foobar', 0]);
        $provider2 = new ArrayProvider(['weak', 'god']);

        $provider = new ChainProvider([$provider1, $provider2]);

        self::assertEquals([$provider1, $provider2], $provider->getProviders());
    }

    /**
     * @test
     */
    public function get_providers()
    {
        $provider = new ChainProvider();

        $provider1 = new ArrayProvider(['test', 'foobar', 0]);
        $provider2 = new ArrayProvider(['weak', 'god']);

        $provider->addProvider($provider1);
        $provider->addProvider($provider2);

        self::assertEquals([$provider1, $provider2], $provider->getProviders());
    }

    /**
     * @test
     */
    public function no_assign_self()
    {
        $provider = new ChainProvider();

        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage('Unable to add ChainProvider to itself.');
        $provider->addProvider($provider);
    }
}
