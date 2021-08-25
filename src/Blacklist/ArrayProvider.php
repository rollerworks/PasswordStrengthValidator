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
 * Array Blacklist Provider.
 *
 * Provides the blacklist from an array.
 */
class ArrayProvider implements BlacklistProviderInterface
{
    private $blacklist = [];

    public function __construct(array $blacklist)
    {
        $this->blacklist = $blacklist;
    }

    public function isBlacklisted($password)
    {
        if (\in_array($password, $this->blacklist, true)) {
            return true;
        }

        return false;
    }
}
