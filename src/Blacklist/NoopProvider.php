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
 * Noop Blacklist Provider.
 *
 * Always returns false.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class NoopProvider implements BlacklistProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function isBlacklisted($password)
    {
        return false;
    }
}
