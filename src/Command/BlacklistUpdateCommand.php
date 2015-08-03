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

use Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\SqliteProvider;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class BlacklistUpdateCommand extends BlacklistCommonCommand
{
    const MESSAGE = '<info>Successfully added %d password(s) to your blacklist database.</info>';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('rollerworks-password:blacklist:update')
            ->setDescription('add new passwords to your blacklist database')
            ->addArgument('passwords', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'space separated list of words to blacklist')
            ->addOption('file', null, InputOption::VALUE_OPTIONAL, 'Text file to import, every line is considered one word')
        ;
    }

    protected function attemptAction(SqliteProvider $service, $password)
    {
        return true === $service->add($password);
    }
}
