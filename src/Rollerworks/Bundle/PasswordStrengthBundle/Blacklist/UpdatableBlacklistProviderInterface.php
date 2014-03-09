<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rollerworks\Bundle\PasswordStrengthBundle\Blacklist;

/**
 * Updatable Blacklist Provider Interface.
 *
 * Allows updating the blacklist.
 * This applies to a DB for example.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
interface UpdatableBlacklistProviderInterface extends ImmutableBlacklistProviderInterface
{
}
