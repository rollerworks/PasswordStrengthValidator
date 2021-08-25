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

use Rollerworks\Component\PasswordStrength\Blacklist\UpdatableBlacklistProviderInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class BlacklistDeleteCommand extends BlacklistCommonCommand
{
    public const MESSAGE = '<info>Successfully removed %d password(s) from your blacklist database.</info>';

    protected function configure()
    {
        $this
            ->setName('rollerworks-password:blacklist:delete')
            ->setDescription('removes passwords from your blacklist database')
            ->addArgument('passwords', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'space separated list of words to remove')
            ->addOption('file', null, InputOption::VALUE_OPTIONAL, 'Text file to read for deletion, every line is considered one word')
        ;
    }

    protected function attemptAction(UpdatableBlacklistProviderInterface $service, $password)
    {
        return $service->isBlacklisted($password) && $service->delete($password) === true;
    }
}
