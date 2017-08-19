<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle package.
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

class ChainProviderTest extends TestCase
{
    public function testBlackList()
    {
        $provider = new ChainProvider();
        $provider->addProvider(new ArrayProvider(array('test', 'foobar', 0)));
        $provider->addProvider(new ArrayProvider(array('weak', 'god')));

        self::assertTrue($provider->isBlacklisted('test'));
        self::assertTrue($provider->isBlacklisted('foobar'));
        self::assertTrue($provider->isBlacklisted(0));

        self::assertTrue($provider->isBlacklisted('weak'));
        self::assertTrue($provider->isBlacklisted('god'));

        self::assertFalse($provider->isBlacklisted('tests'));
        self::assertFalse($provider->isBlacklisted(null));
        self::assertFalse($provider->isBlacklisted(false));
    }

    public function testProvidersByConstruct()
    {
        $provider1 = new ArrayProvider(array('test', 'foobar', 0));
        $provider2 = new ArrayProvider(array('weak', 'god'));

        $provider = new ChainProvider(array($provider1, $provider2));

        self::assertEquals(array($provider1, $provider2), $provider->getProviders());
    }

    public function testGetProviders()
    {
        $provider = new ChainProvider();

        $provider1 = new ArrayProvider(array('test', 'foobar', 0));
        $provider2 = new ArrayProvider(array('weak', 'god'));

        $provider->addProvider($provider1);
        $provider->addProvider($provider2);

        self::assertEquals(array($provider1, $provider2), $provider->getProviders());
    }

    public function testNoAssignSelf()
    {
        $provider = new ChainProvider();

        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage('Unable to add ChainProvider to itself.');
        $provider->addProvider($provider);
    }
}
