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

class BlacklistDeleteCommand extends BlacklistCommonCommand
{
    const MESSAGE = '<info>Successfully removed %d password(s) from your blacklist database.</info>';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('rollerworks-password:blacklist:delete')
            ->setDescription('removes passwords from your blacklist database')
            ->addArgument('passwords', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'space separated list of words to remove')
            ->addOption('file', null, InputOption::VALUE_OPTIONAL, 'Text file to read for deletion, every line is considered one word')
        ;
    }

    protected function attemptAction(SqliteProvider $service, $password)
    {
        return $service->isBlacklisted($password) && true === $service->delete($password);
    }
}
