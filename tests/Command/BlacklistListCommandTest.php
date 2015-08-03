<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\PasswordStrengthBundle\Tests\Command;

use Rollerworks\Bundle\PasswordStrengthBundle\Command\BlacklistListCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class BlacklistListCommandTest extends BlacklistCommandTestCase
{
    public function testList()
    {
        $application = new Application();
        $command = new BlacklistListCommand();
        $command->setContainer(self::$container);
        $application->add($command);

        $command = $application->find('rollerworks-password:blacklist:list');

        $blackListedWords = array('test', 'foobar', 'kaboom');

        foreach ($blackListedWords as $word) {
            $this->getProvider()->add($word);
        }

        foreach ($blackListedWords as $word) {
            $this->assertTrue($this->getProvider()->isBlacklisted($word));
            $this->getProvider()->add($word);
        }

        $commandTester = new CommandTester($command);

        $commandTester->execute(array('command' => $command->getName()));

        $display = $commandTester->getDisplay(true);

        // Words may be displayed in any order, so check each of them
        foreach ($blackListedWords as $word) {
            $this->assertRegExp("/([\n]|^){$word}[\n]/s", $display);
            $this->getProvider()->add($word);
        }
    }
}
