<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rollerworks\Bundle\PasswordStrengthBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

abstract class BlacklistCommand extends ContainerAwareCommand
{
    public function isEnabled()
    {
        if (!$this->getContainer()->has('rollerworks_password_strength.blacklist.provider.sqlite')) {
            return false;
        }

        if (!class_exists('SQLite3') && (!class_exists('PDO') || in_array('sqlite', \PDO::getAvailableDrivers(), true))) {
            return false;
        }

        return true;
    }
}
