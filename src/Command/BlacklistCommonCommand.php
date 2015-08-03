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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BlacklistCommonCommand extends BlacklistCommand
{
    const MESSAGE = 'change me';

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('passwords') && !$input->getOption('file')) {
            $output->writeln('<error>No passwords or file-option given.</error>');

            return;
        }

        /** @var \Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\SqliteProvider $service */
        $service = $this->getContainer()->get('rollerworks_password_strength.blacklist.provider.sqlite');

        if ($input->getOption('file')) {
            $file = realpath($input->getOption('file'));

            if (!file_exists($file)) {
                $output->writeln('<error>Unable to read passwords list. No such file: '.$input->getOption('file').'</error>');

                return;
            }

            if (!is_readable($file)) {
                $output->writeln('<error>Unable to read passwords list. Access denied: '.$input->getOption('file').'</error>');

                return;
            }

            if (filesize($file) == 0) {
                $output->writeln('<comment>Passwords list seems empty, are you sure this is the correct file?</comment>');

                return;
            }

            $count = $this->doFromFile($service, $file);
        } else {
            $count = $this->doFromArray($service, (array) $input->getArgument('passwords'));
        }

        $output->writeln(sprintf(static::MESSAGE, $count));
    }

    /**
     * @param SqliteProvider $service
     * @param string         $filename
     *
     * @return int
     */
    protected function doFromFile(SqliteProvider $service, $filename)
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

    /**
     * @param SqliteProvider $service
     * @param array          $passwords
     *
     * @return int
     */
    protected function doFromArray(SqliteProvider $service, array $passwords)
    {
        $count = 0;
        foreach ($passwords as $password) {
            if ($this->attemptAction($service, $password)) {
                ++$count;
            }
        }

        return $count;
    }

    abstract protected function attemptAction(SqliteProvider $service, $password);
}
