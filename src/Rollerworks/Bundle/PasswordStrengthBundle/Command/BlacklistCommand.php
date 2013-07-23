<?php

namespace Rollerworks\Bundle\PasswordStrengthBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

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
