<?php

/*
 * This file is part of the RollerworksPasswordStrengthValidator package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\PasswordStrength\Blacklist;

use Psr\Container\ContainerInterface;

/**
 * Chained blacklist provider.
 */
final class LazyChainProvider implements BlacklistProviderInterface
{
    private $container;
    private $providers;

    /**
     * Constructor.
     *
     * @param string[] $providers
     */
    public function __construct(ContainerInterface $container, array $providers)
    {
        $this->container = $container;
        $this->providers = $providers;
    }

    /**
     * {@inheritdoc}
     *
     * Runs trough all the providers until one returns true.
     */
    public function isBlacklisted($password)
    {
        foreach ($this->providers as $provider) {
            if ($this->container->get($provider)->isBlacklisted($password) === true) {
                return true;
            }
        }

        return false;
    }
}
