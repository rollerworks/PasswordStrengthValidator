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

/**
 * Chained blacklist provider.
 */
class ChainProvider implements BlacklistProviderInterface
{
    /**
     * @var BlacklistProviderInterface[]
     */
    private $providers;

    /**
     * Constructor.
     *
     * @param BlacklistProviderInterface[] $providers
     */
    public function __construct(array $providers = [])
    {
        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }
    }

    /**
     * Adds a new blacklist provider.
     *
     * @throws \RuntimeException
     *
     * @return self
     */
    public function addProvider(BlacklistProviderInterface $provider)
    {
        if ($provider === $this) {
            throw new \RuntimeException('Unable to add ChainProvider to itself.');
        }

        $this->providers[] = $provider;

        return $this;
    }

    /**
     * Returns all the registered providers.
     *
     * @return BlacklistProviderInterface[]
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * Runs trough all the providers until one returns true.
     *
     * {@inheritdoc}
     */
    public function isBlacklisted($password)
    {
        foreach ($this->providers as $provider) {
            if ($provider->isBlacklisted($password) === true) {
                return true;
            }
        }

        return false;
    }
}
