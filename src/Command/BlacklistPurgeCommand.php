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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BlacklistPurgeCommand extends BlacklistCommand
{
    protected function configure()
    {
        $this
            ->setName('rollerworks-password:blacklist:purge')
            ->setDescription('removes all passwords from your blacklist database')
            ->addOption('no-ask', null, InputOption::VALUE_NONE, 'Don\'t ask for confirmation')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (! $input->getOption('no-ask')) {
            $io->warning('This will remove all the passwords from your blacklist.');

            if (! $io->confirm('Are you sure you want to purge the blacklist?', false)) {
                return 1;
            }
        }

        $this->blacklistProvider->purge();

        $io->success('Successfully removed all passwords from your blacklist.');

        return 0;
    }
}
