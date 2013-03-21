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
 * Blacklist Provider Interface.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
interface BlacklistProviderInterface
{
    /**
     * Returns whether the provided password is blacklisted.
     *
     * @param string $password
     *
     * @return boolean
     */
    public function isBlacklisted($password);
}
