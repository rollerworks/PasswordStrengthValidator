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

use Rollerworks\Bundle\PasswordStrengthBundle\Command\BlacklistPurgeCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class BlacklistPurgeCommandTest extends BlacklistCommandTestCase
{
    public function testWithAsk()
    {
        $application = new Application();
        $command = new BlacklistPurgeCommand();
        $command->setContainer(self::$container);
        $application->add($command);

        $command = $application->find('rollerworks-password:blacklist:purge');

        $this->getProvider()->add('test');
        $this->getProvider()->add('foobar');
        $this->getProvider()->add('kaboom');

        $this->assertTrue($this->getProvider()->isBlacklisted('test'));
        $this->assertTrue($this->getProvider()->isBlacklisted('foobar'));
        $this->assertTrue($this->getProvider()->isBlacklisted('kaboom'));

        $commandTester = new CommandTester($command);

        $dialog = $command->getHelperSet()->get('dialog');
        $dialog->setInputStream($this->getInputStream("n\nno\n"));

        $commandTester->execute(array('command' => $command->getName()), array('interactive' => true));

        $this->assertRegExp('/This will remove all the passwords from your blacklist database!!/', $commandTester->getDisplay());

        $this->assertTrue($this->getProvider()->isBlacklisted('test'));
        $this->assertTrue($this->getProvider()->isBlacklisted('foobar'));
        $this->assertTrue($this->getProvider()->isBlacklisted('kaboom'));
    }

    public function testWithAskConfirm()
    {
        $application = new Application();
        $command = new BlacklistPurgeCommand();
        $command->setContainer(self::$container);
        $application->add($command);

        $command = $application->find('rollerworks-password:blacklist:purge');

        $this->getProvider()->add('test');
        $this->getProvider()->add('foobar');
        $this->getProvider()->add('kaboom');

        $this->assertTrue($this->getProvider()->isBlacklisted('test'));
        $this->assertTrue($this->getProvider()->isBlacklisted('foobar'));
        $this->assertTrue($this->getProvider()->isBlacklisted('kaboom'));

        $commandTester = new CommandTester($command);

        $dialog = $command->getHelperSet()->get('dialog');
        $dialog->setInputStream($this->getInputStream("y\nyes\n"));

        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('/This will remove all the passwords from your blacklist database!!/', $commandTester->getDisplay());

        $this->assertFalse($this->getProvider()->isBlacklisted('test'));
        $this->assertFalse($this->getProvider()->isBlacklisted('foobar'));
        $this->assertFalse($this->getProvider()->isBlacklisted('kaboom'));
    }

    public function testNoAsk()
    {
        $application = new Application();
        $command = new BlacklistPurgeCommand();
        $command->setContainer(self::$container);
        $application->add($command);

        $command = $application->find('rollerworks-password:blacklist:purge');

        $this->getProvider()->add('test');
        $this->getProvider()->add('foobar');
        $this->getProvider()->add('kaboom');

        $this->assertTrue($this->getProvider()->isBlacklisted('test'));
        $this->assertTrue($this->getProvider()->isBlacklisted('foobar'));
        $this->assertTrue($this->getProvider()->isBlacklisted('kaboom'));

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '--no-ask' => null));

        $this->assertFalse($this->getProvider()->isBlacklisted('test'));
        $this->assertFalse($this->getProvider()->isBlacklisted('foobar'));
        $this->assertFalse($this->getProvider()->isBlacklisted('kaboom'));
    }

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fwrite($stream, $input);
        rewind($stream);

        return $stream;
    }
}
