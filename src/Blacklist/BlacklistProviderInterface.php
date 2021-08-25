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
 * Blacklist Provider Interface.
 */
interface BlacklistProviderInterface
{
    /**
     * Returns whether the provided password is blacklisted.
     *
     * @param string $password
     *
     * @return bool
     */
    public function isBlacklisted($password);
}
