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
use Rollerworks\Component\PasswordStrength\Blacklist\BlacklistProviderInterface;
use Rollerworks\Component\PasswordStrength\Blacklist\LazyChainProvider;
use Rollerworks\Component\PasswordStrength\Tests\BlackListMockProviderTrait;

/**
 * @internal
 */
final class LazyChainProviderTest extends TestCase
{
    use BlackListMockProviderTrait;

    /**
     * @test
     */
    public function black_list()
    {
        $loader1 = $this->createMockedProvider('foobar');
        $loader2 = $this->createMockedProvider('god');

        $this->createLoadersContainer(['first' => $loader1, 'second' => $loader2]);

        $provider = new LazyChainProvider(
            $this->createLoadersContainer(['first' => $loader1, 'second' => $loader2]),
            ['first', 'second']
        );

        self::assertTrue($provider->isBlacklisted('god'));
        self::assertTrue($provider->isBlacklisted('foobar'));

        self::assertFalse($provider->isBlacklisted('tests'));
        self::assertFalse($provider->isBlacklisted(null));
        self::assertFalse($provider->isBlacklisted(false));
    }

    /**
     * @test
     */
    public function stops_loading_on_first_hit()
    {
        $loader1 = $this->createMockedProvider('foobar');
        $loader2 = $this->createNotExpectedMockedProvider();

        $this->createLoadersContainer(['first' => $loader1, 'second' => $loader2]);

        $provider = new LazyChainProvider(
            $this->createLoadersContainer(['first' => $loader1, 'second' => $loader2]),
            ['first', 'second']
        );

        self::assertTrue($provider->isBlacklisted('foobar'));
    }

    protected function createNotExpectedMockedProvider()
    {
        // Prophesize acts funny...
        $mock = $this->createMock(BlacklistProviderInterface::class);
        $mock->expects(self::never())->method('isBlacklisted');

        return $mock;
    }
}
