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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BlacklistPurgeCommand extends BlacklistCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('rollerworks-password:blacklist:purge')
            ->setDescription('removes all passwords from your blacklist database')
            ->addOption('no-ask', null, InputOption::VALUE_NONE, 'Don\'t ask for confirmation')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\SqliteProvider $service */
        $service = $this->getContainer()->get('rollerworks_password_strength.blacklist.provider.sqlite');

        if (!$input->getOption('no-ask')) {
            /** @var \Symfony\Component\Console\Helper\DialogHelper $dialog */
            $dialog = $this->getHelperSet()->get('dialog');

            if (!$dialog->askConfirmation($output, '<question>This will remove all the passwords from your blacklist database!!, continue?</question>', false)) {
                return;
            }
        }

        $service->purge();
        $output->writeln('<info>Successfully removed all passwords from your blacklist database.</info>');
    }
}
