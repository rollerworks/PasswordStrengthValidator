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
 * Noop Blacklist Provider.
 *
 * Always returns false.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class NoopProvider implements BlacklistProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function isBlacklisted($password)
    {
        return false;
    }
}
