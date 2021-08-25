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

use Rollerworks\Component\PasswordStrength\Command\BlacklistListCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 */
final class BlacklistCommandTest extends BlacklistCommandTestCase
{
    /**
     * @test
     */
    public function validates_provider_must_be_updatable()
    {
        $container = $this->createLoadersContainer([
            'default' => self::$blackListProvider,
            'second' => $this->createMockedProvider('nope'),
        ]);

        $application = new Application();
        $application->add(new BlacklistListCommand($container));

        $commandTester = new CommandTester($application->find('rollerworks-password:blacklist:list'));

        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage('Blacklist provider "second" is not updatable.');

        $commandTester->execute(['command' => $application->find('rollerworks-password:blacklist:list')->getName(), '--provider' => 'second']);
    }

    /**
     * @test
     */
    public function second_provider_is_used()
    {
        $blackListedWords = ['test', 'foobar', 'kaboom'];

        foreach ($blackListedWords as $word) {
            self::$blackListProvider->add($word);
        }

        $container = $this->createLoadersContainer([
            'default' => $this->createMockedProvider('nope'),
            'second' => self::$blackListProvider,
        ]);

        $application = new Application();
        $application->add(new BlacklistListCommand($container));

        $commandTester = new CommandTester($application->find('rollerworks-password:blacklist:list'));
        $commandTester->execute(['command' => $application->find('rollerworks-password:blacklist:list')->getName(), '--provider' => 'second']);

        $display = $commandTester->getDisplay(true);

        // Words may be displayed in any order, so check each of them
        foreach ($blackListedWords as $word) {
            self::assertMatchesRegularExpression("/([\n]|^){$word}[\n]/s", $display);
            self::$blackListProvider->add($word);
        }
    }
}
