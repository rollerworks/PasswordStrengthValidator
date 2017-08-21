<?php

/*
 * This file is part of the RollerworksPasswordStrengthValidator package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\PasswordStrength\Tests;

use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use Rollerworks\Component\PasswordStrength\Blacklist\BlacklistProviderInterface;

/**
 * Trait BlackListMockProviderTrait.
 *
 * @method ObjectProphecy prophesize($class)
 */
trait BlackListMockProviderTrait
{
    protected function createMockedProvider($blacklisted)
    {
        $mockProvider = $this->prophesize(BlacklistProviderInterface::class);
        $mockProvider->isBlacklisted($blacklisted)->willReturn(true);
        $mockProvider->isBlacklisted(Argument::any())->willReturn(false);

        return $mockProvider->reveal();
    }

    /**
     * @param array $loaders
     *
     * @return ContainerInterface|object
     */
    protected function createLoadersContainer(array $loaders)
    {
        $loadersProphecy = $this->prophesize(ContainerInterface::class);
        $loadersProphecy->has(Argument::any())->willReturn(false);

        foreach ($loaders as $name => $loader) {
            $loadersProphecy->has($name)->willReturn(true);
            $loadersProphecy->get($name)->willReturn($loader);
        }

        return $loadersProphecy->reveal();
    }
}
