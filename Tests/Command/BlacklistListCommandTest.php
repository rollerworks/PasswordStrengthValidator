<?php

namespace Rollerworks\Bundle\PasswordStrengthBundle\Tests\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Rollerworks\Bundle\PasswordStrengthBundle\Command\BlacklistListCommand;

class BlacklistListCommandTest extends BlacklistCommandTestCase
{
    public function testList()
    {
        $application = new Application();
        $command = new BlacklistListCommand();
        $command->setContainer(self::$container);
        $application->add($command);

        $command = $application->find('rollerworks-password:blacklist:list');

        $this->getProvider()->add('test');
        $this->getProvider()->add('foobar');
        $this->getProvider()->add('kaboom');

        $this->assertTrue($this->getProvider()->isBlacklisted('test'));
        $this->assertTrue($this->getProvider()->isBlacklisted('foobar'));
        $this->assertTrue($this->getProvider()->isBlacklisted('kaboom'));

        $commandTester = new CommandTester($command);

        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp("/^test[\r\n]{1,2}foobar[\r\n]{1,2}kaboom[\r\n]{1,2}$/s", $commandTester->getDisplay());
    }
}
