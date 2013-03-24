<?php

namespace Rollerworks\Bundle\PasswordStrengthBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\SqliteProvider;

class BlacklistUpdateCommand extends BlacklistCommand
{
    protected function configure()
    {
        $this
            ->setName('rollerworks-password:blacklist:update')->setDescription('add new passwords to your blacklist database')
            ->addArgument('passwords', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'space separated list of words to blacklist')
            ->addOption('file', null, null, 'Text file to import, every line is considered one word');
    }

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

            $count = $this->importFromFile($service, $file);
        } else {
            $count = $this->importFromArray($service, (array) $input->getArgument('passwords'));
        }

        $output->writeln(sprintf('<info>Successfully added %d password(s) to your blacklist database.</info>', $count));
    }

    /**
     * @param SqliteProvider $service
     * @param string         $filename
     *
     * @return int
     */
    protected function importFromFile(SqliteProvider $service, $filename)
    {
        ini_set('auto_detect_line_endings', true);

        if (!($file = fopen($filename, 'r'))) {
            return 0;
        }

        $count = 0;

        while (!feof($file)) {
            $password = trim(fgets($file), "\n\r");
            if (true === $service->add($password)) {
                $count++;
            }
        }

        @fclose($file);

        return $count;
    }

    /**
     * @param SqliteProvider $service
     * @param array          $passwords
     *
     * @return integer
     */
    protected function importFromArray(SqliteProvider $service, array $passwords)
    {
        $count = 0;
        foreach ($passwords as $password) {
            if (true === $service->add($password)) {
                $count++;
            }
        }

        return $count;
    }
}
