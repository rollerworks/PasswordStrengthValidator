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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class BlacklistCommonCommand extends BlacklistCommand
{
    public const MESSAGE = '%d';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (! $input->getArgument('passwords') && ! $input->getOption('file')) {
            $io->error('No passwords or file-option given.');

            return 1;
        }

        if ($input->getOption('file')) {
            $file = realpath($input->getOption('file'));

            if (! file_exists($file)) {
                $io->error('Unable to read passwords list. No such file: ' . $input->getOption('file'));

                return 1;
            }

            if (! is_readable($file)) {
                $io->error('Unable to read passwords list. Access denied: ' . $input->getOption('file'));

                return 1;
            }

            if (filesize($file) === 0) {
                $io->note('Passwords list seems empty, are you sure this is the correct file?');

                return 1;
            }

            $count = $this->doFromFile($this->blacklistProvider, $file);
        } else {
            $count = $this->doFromArray($this->blacklistProvider, (array) $input->getArgument('passwords'));
        }

        $output->writeln(sprintf(static::MESSAGE, $count));

        return 0;
    }

    abstract protected function attemptAction(UpdatableBlacklistProviderInterface $service, $password);

    private function doFromFile(UpdatableBlacklistProviderInterface $service, $filename)
    {
        $file = new \SplFileObject($filename, 'r');
        $count = 0;

        foreach ($file as $password) {
            $password = trim($password, "\n\r");

            if ($this->attemptAction($service, $password)) {
                ++$count;
            }
        }

        // close file object
        $file = null;

        return $count;
    }

    private function doFromArray(UpdatableBlacklistProviderInterface $service, array $passwords)
    {
        $count = 0;

        foreach ($passwords as $password) {
            if ($this->attemptAction($service, $password)) {
                ++$count;
            }
        }

        return $count;
    }
}
