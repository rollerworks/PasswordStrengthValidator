<?php

namespace Rollerworks\Bundle\PasswordStrengthBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
