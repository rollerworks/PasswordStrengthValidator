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
 * Updatable Blacklist Provider marks the blacklist provider is updatable.
 */
interface UpdatableBlacklistProviderInterface extends BlacklistProviderInterface
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
     *
     * @return bool
     */
    public function delete($password);

    /**
     * Returns an array or Traversable object with all the blacklisted passwords.
     *
     * @return array|\Traversable|iterable
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
     * Used for closing a stream resource or database connection.
     */
    public function close($db);
}
