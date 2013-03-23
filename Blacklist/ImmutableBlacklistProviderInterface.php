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
 * Immutable Blacklist Provider Interface.
 *
 * Allows changing the blacklists state.
 * This applies to a DB for example.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
interface ImmutableBlacklistProviderInterface extends BlacklistProviderInterface
{
    /**
     * Adds a word to the blacklist.
     *
     * @param string $password
     *
     * @return boolean|integer Returns true on success, false on error and -1 when already existent
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
     */
    public function close($db);
}
