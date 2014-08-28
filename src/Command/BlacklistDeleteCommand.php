<?php

/**
 * This file is part of the RollerworksPasswordStrengthBundle package.
 *
 * (c) 2012-2014 Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\PasswordStrengthBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\SqliteProvider;

class BlacklistDeleteCommand extends BlacklistCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('rollerworks-password:blacklist:delete')
            ->setDescription('removes passwords from your blacklist database')
            ->addArgument('passwords', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'space separated list of words to remove')
            ->addOption('file', null, null, 'Text file to read for deletion, every line is considered one word')
        ;
    }

    /**
     * {@inheritDoc}
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
                $output->writeln('<error>Unable to read passwords list. No such file: ' . $input->getOption('file') . '</error>');

                return;
            } elseif (!is_readable($file)) {
                $output->writeln('<error>Unable to read passwords list. Access denied: ' . $input->getOption('file') . '</error>');

                return;
            } elseif (filesize($file) == 0) {
                $output->writeln('<comment>Passwords list seems empty, are you sure this is the correct file?</comment>');

                return;
            }

            $count = $this->readFromFile($service, $file);
        } else {
            $count = $this->readFromArray($service, (array) $input->getArgument('passwords'));
        }

        $output->writeln(sprintf('<info>Successfully removed %d password(s) from your blacklist database.</info>', $count));
    }

    /**
     * @param SqliteProvider $service
     * @param string         $filename
     *
     * @return integer
     */
    protected function readFromFile(SqliteProvider $service, $filename)
    {
        $file = new \SplFileObject($filename, 'r');
        $count = 0;

        foreach ($file as $password) {
            $password = trim($password, "\n\r");
            if ($service->isBlacklisted($password) && true === $service->delete($password)) {
                $count++;
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
     * @return integer
     */
    protected function readFromArray(SqliteProvider $service, array $passwords)
    {
        $count = 0;
        foreach ($passwords as $password) {
            if ($service->isBlacklisted($password) && true === $service->delete($password)) {
                $count++;
            }
        }

        return $count;
    }
}
