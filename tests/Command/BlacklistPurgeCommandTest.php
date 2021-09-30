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

use Rollerworks\Component\PasswordStrength\Command\BlacklistPurgeCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 * @group legacy
 */
final class BlacklistPurgeCommandTest extends BlacklistCommandTestCase
{
    /**
     * @test
     */
    public function with_ask()
    {
        $command = $this->getCommand();

        self::$blackListProvider->add('test');
        self::$blackListProvider->add('foobar');
        self::$blackListProvider->add('kaboom');

        self::assertTrue(self::$blackListProvider->isBlacklisted('test'));
        self::assertTrue(self::$blackListProvider->isBlacklisted('foobar'));
        self::assertTrue(self::$blackListProvider->isBlacklisted('kaboom'));

        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['no']);
        $commandTester->execute(['command' => $command->getName()], ['interactive' => true]);

        $display = $commandTester->getDisplay(true);
        self::assertMatchesRegularExpression('/This will remove all the passwords from your blacklist./', $display);
        self::assertMatchesRegularExpression('/Are you sure you want to purge the blacklist\?/', $display);

        self::assertDoesNotMatchRegularExpression('/Successfully removed all passwords from your blacklist\./', $commandTester->getDisplay(true));

        self::assertTrue(self::$blackListProvider->isBlacklisted('test'));
        self::assertTrue(self::$blackListProvider->isBlacklisted('foobar'));
        self::assertTrue(self::$blackListProvider->isBlacklisted('kaboom'));
    }

    /**
     * @test
     */
    public function with_ask_confirm()
    {
        $command = $this->getCommand();

        self::$blackListProvider->add('test');
        self::$blackListProvider->add('foobar');
        self::$blackListProvider->add('kaboom');

        self::assertTrue(self::$blackListProvider->isBlacklisted('test'));
        self::assertTrue(self::$blackListProvider->isBlacklisted('foobar'));
        self::assertTrue(self::$blackListProvider->isBlacklisted('kaboom'));

        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['y', 'yes']);
        $commandTester->execute(['command' => $command->getName()]);

        $display = $commandTester->getDisplay(true);
        self::assertMatchesRegularExpression('/This will remove all the passwords from your blacklist\./', $display);
        self::assertMatchesRegularExpression('/Are you sure you want to purge the blacklist\?/', $display);

        self::assertMatchesRegularExpression('/Successfully removed all passwords from your blacklist\./', $commandTester->getDisplay(true));

        self::assertFalse(self::$blackListProvider->isBlacklisted('test'));
        self::assertFalse(self::$blackListProvider->isBlacklisted('foobar'));
        self::assertFalse(self::$blackListProvider->isBlacklisted('kaboom'));
    }

    /**
     * @test
     */
    public function no_ask()
    {
        $command = $this->getCommand();

        self::$blackListProvider->add('test');
        self::$blackListProvider->add('foobar');
        self::$blackListProvider->add('kaboom');

        self::assertTrue(self::$blackListProvider->isBlacklisted('test'));
        self::assertTrue(self::$blackListProvider->isBlacklisted('foobar'));
        self::assertTrue(self::$blackListProvider->isBlacklisted('kaboom'));

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), '--no-ask' => null]);

        self::assertMatchesRegularExpression('/Successfully removed all passwords from your blacklist\./', $commandTester->getDisplay(true));

        self::assertFalse(self::$blackListProvider->isBlacklisted('test'));
        self::assertFalse(self::$blackListProvider->isBlacklisted('foobar'));
        self::assertFalse(self::$blackListProvider->isBlacklisted('kaboom'));
    }

    private function getCommand()
    {
        $application = new Application();
        $application->add(
            new BlacklistPurgeCommand($this->createLoadersContainer(['default' => self::$blackListProvider]))
        );

        return $application->find('rollerworks-password:blacklist:purge');
    }
}
