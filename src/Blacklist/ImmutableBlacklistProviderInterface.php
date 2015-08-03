<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\PasswordStrengthBundle\Blacklist;

/**
 * Immutable Blacklist Provider Interface.
 *
 * Allows changing the blacklist state.
 * This applies to a DB for example.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * @deprecated This class is deprecated and will be removed in version 2.0
 *  use the UpdatableBlacklistProviderInterface instead.
 */
interface ImmutableBlacklistProviderInterface extends BlacklistProviderInterface
{
    /**
     * Adds a word to the blacklist.
     *
     * @param string $password
     *
     * @return bool|int Returns true on success, false on error and -1 when already existent
     */
    public function add($password);

    /**
     * Deletes a word from the blacklist.
     *
     * @param string $password
     */
    public function delete($password);

    /**
     * Returns an array or Traversable object with all the blacklisted passwords.
     *
     * @return array|\Traversable
     */
    public function all();

    /**
     * Deletes all the blacklisted passwords.
     */
    public function purge();

    /**
     * Closes the list mutation-operation.
     *
     * This depends on the implementation.
     * Use for closing either stream resource or database connection.
     *
     * @param mixed $db
     */
    public function close($db);
}
