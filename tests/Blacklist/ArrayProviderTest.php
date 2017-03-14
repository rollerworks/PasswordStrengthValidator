<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\PasswordStrengthBundle\Tests\Blacklist;

use PHPUnit\Framework\TestCase;
use Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\ArrayProvider;

class ArrayProviderTest extends TestCase
{
    public function testBlackList()
    {
        $provider = new ArrayProvider(array('test', 'foobar', 0));

        self::assertTrue($provider->isBlacklisted('test'));
        self::assertTrue($provider->isBlacklisted('foobar'));
        self::assertTrue($provider->isBlacklisted(0));

        self::assertFalse($provider->isBlacklisted('tests'));
        self::assertFalse($provider->isBlacklisted(null));
        self::assertFalse($provider->isBlacklisted(false));
    }
}
