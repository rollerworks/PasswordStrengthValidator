<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
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
