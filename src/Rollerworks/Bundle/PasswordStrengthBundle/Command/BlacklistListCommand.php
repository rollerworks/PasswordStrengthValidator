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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BlacklistListCommand extends BlacklistCommand
{
    protected function configure()
    {
        $this
            ->setName('rollerworks-password:blacklist:list')->setDescription('lists all blacklisted passwords from the database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\SqliteProvider $service */
        $service = $this->getContainer()->get('rollerworks_password_strength.blacklist.provider.sqlite');

        foreach ($service->all() as $password) {
            $output->writeln($password, OutputInterface::OUTPUT_RAW);
        }
    }
}
