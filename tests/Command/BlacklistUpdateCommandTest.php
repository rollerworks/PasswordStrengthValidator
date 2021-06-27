<?php

/*
 * This file is part of the RollerworksPasswordStrengthValidator package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\PasswordStrength\Tests\Command;

use Rollerworks\Component\PasswordStrength\Command\BlacklistUpdateCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class BlacklistUpdateCommandTest extends BlacklistCommandTestCase
{
    public function testAddOneWord()
    {
        $command = $this->getCommand();

        self::assertFalse(self::$blackListProvider->isBlacklisted('test'));

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'passwords' => 'test']);

        self::assertTrue(self::$blackListProvider->isBlacklisted('test'));
        self::assertMatchesRegularExpression('/Successfully added 1 password\(s\) to your blacklist database/', $commandTester->getDisplay());
    }

    public function testAddExistingWord()
    {
        $command = $this->getCommand();

        self::assertFalse(self::$blackListProvider->isBlacklisted('test'));

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'passwords' => 'test']);

        self::assertTrue(self::$blackListProvider->isBlacklisted('test'));
        self::assertMatchesRegularExpression('/Successfully added 1 password\(s\) to your blacklist database/', $commandTester->getDisplay());

        self::assertTrue(self::$blackListProvider->isBlacklisted('test'));
        $commandTester->execute(['command' => $command->getName(), 'passwords' => 'test']);

        self::assertTrue(self::$blackListProvider->isBlacklisted('test'));
        self::assertMatchesRegularExpression('/Successfully added 0 password\(s\) to your blacklist database/', $commandTester->getDisplay());
    }

    public function testAddTwoWords()
    {
        $command = $this->getCommand();

        self::assertFalse(self::$blackListProvider->isBlacklisted('test'));
        self::assertFalse(self::$blackListProvider->isBlacklisted('foobar'));

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'passwords' => ['test', 'foobar']]);

        self::assertTrue(self::$blackListProvider->isBlacklisted('test'));
        self::assertTrue(self::$blackListProvider->isBlacklisted('foobar'));
        self::assertMatchesRegularExpression('/Successfully added 2 password\(s\) to your blacklist database/', $commandTester->getDisplay());
    }

    public function testNoInput()
    {
        $command = $this->getCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        self::assertDoesNotMatchRegularExpression('/Successfully added \d+ password\(s\) to your blacklist database/', $commandTester->getDisplay());
        self::assertMatchesRegularExpression('/No passwords or file-option given/', $commandTester->getDisplay());
    }

    public function testImportFromFile()
    {
        $command = $this->getCommand();

        self::assertFalse(self::$blackListProvider->isBlacklisted('test'));
        self::assertFalse(self::$blackListProvider->isBlacklisted('foobar'));

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), '--file' => __DIR__.'/../fixtures/passwords-list1.txt']);

        self::assertTrue(self::$blackListProvider->isBlacklisted('test'));
        self::assertTrue(self::$blackListProvider->isBlacklisted('foobar'));
        self::assertMatchesRegularExpression('/Successfully added 2 password\(s\) to your blacklist database/', $commandTester->getDisplay());
    }

    public function testImportExistingFromFile()
    {
        $command = $this->getCommand();

        self::assertFalse(self::$blackListProvider->isBlacklisted('test'));
        self::assertFalse(self::$blackListProvider->isBlacklisted('foobar'));

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), '--file' => __DIR__.'/../fixtures/passwords-list1.txt']);

        self::assertTrue(self::$blackListProvider->isBlacklisted('test'));
        self::assertTrue(self::$blackListProvider->isBlacklisted('foobar'));
        self::assertMatchesRegularExpression('/Successfully added 2 password\(s\) to your blacklist database/', $commandTester->getDisplay());

        self::assertTrue(self::$blackListProvider->isBlacklisted('test'));
        self::assertTrue(self::$blackListProvider->isBlacklisted('foobar'));
        $commandTester->execute(['command' => $command->getName(), '--file' => __DIR__.'/../fixtures/passwords-list1.txt']);

        self::assertTrue(self::$blackListProvider->isBlacklisted('test'));
        self::assertTrue(self::$blackListProvider->isBlacklisted('foobar'));
        self::assertMatchesRegularExpression('/Successfully added 0 password\(s\) to your blacklist database/', $commandTester->getDisplay());
    }

    public function testImportFromRelFile()
    {
        $command = $this->getCommand();

        self::assertFalse(self::$blackListProvider->isBlacklisted('test'));
        self::assertFalse(self::$blackListProvider->isBlacklisted('foobar'));

        // This changes the current working directory to this one so we can check relative files
        chdir(__DIR__);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            ['command' => $command->getName(), '--file' => '../fixtures/passwords-list1.txt']
        );

        self::assertMatchesRegularExpression('/Successfully added 2 password\(s\) to your blacklist database/', $commandTester->getDisplay());
        self::assertTrue(self::$blackListProvider->isBlacklisted('test'));
        self::assertTrue(self::$blackListProvider->isBlacklisted('foobar'));
    }

    public function testImportFromNoFile()
    {
        $command = $this->getCommand();

        self::assertFalse(self::$blackListProvider->isBlacklisted('test'));
        self::assertFalse(self::$blackListProvider->isBlacklisted('foobar'));

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            ['command' => $command->getName(), '--file' => '../fixtures/unknown.txt']
        );

        self::assertMatchesRegularExpression('#Unable to read passwords list. No such file: \.\./fixtures/unknown\.txt#', $commandTester->getDisplay());
    }

    public function testImportFromEmptyFile()
    {
        $command = $this->getCommand();

        self::assertFalse(self::$blackListProvider->isBlacklisted('test'));
        self::assertFalse(self::$blackListProvider->isBlacklisted('foobar'));

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            ['command' => $command->getName(), '--file' => __DIR__.'/../fixtures/passwords-list2.txt']
        );

        self::assertMatchesRegularExpression('/Passwords list seems empty, are you sure this is the correct file\?/', $commandTester->getDisplay());
    }

    private function getCommand()
    {
        $application = new Application();
        $application->add(
            new BlacklistUpdateCommand($this->createLoadersContainer(['default' => self::$blackListProvider]))
        );

        return $application->find('rollerworks-password:blacklist:update');
    }
}
