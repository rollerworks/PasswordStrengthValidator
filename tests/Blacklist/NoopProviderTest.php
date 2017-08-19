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
use Rollerworks\Component\PasswordStrength\Blacklist\NoopProvider;

class NoopProviderTest extends TestCase
{
    public function testBlackList()
    {
        $provider = new NoopProvider();

        self::assertFalse($provider->isBlacklisted('test'));
        self::assertFalse($provider->isBlacklisted('foobar'));
        self::assertFalse($provider->isBlacklisted(0));
        self::assertFalse($provider->isBlacklisted('tests'));
        self::assertFalse($provider->isBlacklisted(null));
        self::assertFalse($provider->isBlacklisted(false));
    }
}
