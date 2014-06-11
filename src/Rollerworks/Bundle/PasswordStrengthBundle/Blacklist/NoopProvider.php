<?php

/**
 * This file is part of the RollerworksPasswordStrengthBundle package.
 *
 * (c) 2012-2014 Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
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
