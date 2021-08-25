<?php

/*
 * This file is part of the RollerworksPasswordStrengthValidator package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\PasswordStrength\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BlacklistListCommand extends BlacklistCommand
{
    protected function configure()
    {
        $this
            ->setName('rollerworks-password:blacklist:list')
            ->setDescription('lists all blacklisted passwords from the database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->blacklistProvider->all() as $password) {
            $output->writeln($password, OutputInterface::OUTPUT_RAW);
        }

        return 0;
    }
}
