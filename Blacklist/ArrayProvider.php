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
 * Array Blacklist Provider.
 *
 * Provides the blacklist from an array.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class ArrayProvider implements BlacklistProviderInterface
{
    private $blacklist = array();

    /**
     * @param array $blacklist
     */
    public function __construct(array $blacklist)
    {
        $this->blacklist = $blacklist;
    }

    /**
     * {@inheritDoc}
     */
    public function isBlacklisted($password)
    {
        if (in_array($password, $this->blacklist, true)) {
            return true;
        }

        return false;
    }
}
