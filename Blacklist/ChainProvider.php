<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rollerworks\Bundle\PasswordStrengthBundle\Blacklist;

/**
 * Chained blacklist provider.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
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
    public function __construct(array $providers = array())
    {
        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }
    }

    /**
     * Adds a new blacklist provider.
     *
     * @param BlacklistProviderInterface $provider
     *
     * @throws \RuntimeException
     *
     * @return self
     */
    public function addProvider(BlacklistProviderInterface $provider)
    {
        if ($provider === $this) {
            throw new \RuntimeException('Unable to add ChainProvider to its self.');
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
     * Runs trough all the providers till one returns true.
     *
     * @param string $password
     *
     * @return boolean
     */
    public function isBlacklisted($password)
    {
        foreach ($this->providers as $provider) {
            if (true === $provider->isBlacklisted($password)) {
                return true;
            }
        }

        return false;
    }
}
