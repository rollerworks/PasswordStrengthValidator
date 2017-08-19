<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle package.
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

class BlacklistListCommandTest extends BlacklistCommandTestCase
{
    public function testList()
    {
        $application = new Application();
        $command = new BlacklistListCommand(self::$blackListProvider);
        $application->add($command);

        $command = $application->find('rollerworks-password:blacklist:list');

        $blackListedWords = array('test', 'foobar', 'kaboom');

        foreach ($blackListedWords as $word) {
            self::$blackListProvider->add($word);
        }

        foreach ($blackListedWords as $word) {
            self::assertTrue(self::$blackListProvider->isBlacklisted($word));
            self::$blackListProvider->add($word);
        }

        $commandTester = new CommandTester($command);

        $commandTester->execute(array('command' => $command->getName()));

        $display = $commandTester->getDisplay(true);

        // Words may be displayed in any order, so check each of them
        foreach ($blackListedWords as $word) {
            self::assertRegExp("/([\n]|^){$word}[\n]/s", $display);
            self::$blackListProvider->add($word);
        }
    }
}
