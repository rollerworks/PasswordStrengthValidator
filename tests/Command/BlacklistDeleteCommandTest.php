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

use Rollerworks\Bundle\PasswordStrengthBundle\Command\BlacklistDeleteCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class BlacklistDeleteCommandTest extends BlacklistCommandTestCase
{
    public function testDeleteOneWord()
    {
        $application = new Application();
        $command = new BlacklistDeleteCommand();
        $command->setContainer(self::$container);
        $application->add($command);

        $command = $application->find('rollerworks-password:blacklist:delete');

        $this->getProvider()->add('test');
        $this->getProvider()->add('foobar');

        $this->assertTrue($this->getProvider()->isBlacklisted('test'));
        $this->assertTrue($this->getProvider()->isBlacklisted('foobar'));

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), 'passwords' => 'test'));

        $this->assertFalse($this->getProvider()->isBlacklisted('test'));
        $this->assertTrue($this->getProvider()->isBlacklisted('foobar'));

        $this->assertRegExp('/Successfully removed 1 password\(s\) from your blacklist database/', $commandTester->getDisplay());
    }

    public function testDeleteNoneExistingWord()
    {
        $application = new Application();
        $command = new BlacklistDeleteCommand();
        $command->setContainer(self::$container);
        $application->add($command);

        $command = $application->find('rollerworks-password:blacklist:delete');

        $this->assertFalse($this->getProvider()->isBlacklisted('test'));

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), 'passwords' => 'test'));

        $this->assertFalse($this->getProvider()->isBlacklisted('test'));
        $this->assertRegExp('/Successfully removed 0 password\(s\) from your blacklist database/', $commandTester->getDisplay());
    }

    public function testDeleteTwoWords()
    {
        $application = new Application();
        $command = new BlacklistDeleteCommand();
        $command->setContainer(self::$container);
        $application->add($command);

        $command = $application->find('rollerworks-password:blacklist:delete');

        $this->getProvider()->add('test');
        $this->getProvider()->add('foobar');
        $this->getProvider()->add('kaboom');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), 'passwords' => array('test', 'foobar')));

        $this->assertFalse($this->getProvider()->isBlacklisted('test'));
        $this->assertFalse($this->getProvider()->isBlacklisted('foobar'));
        $this->assertTrue($this->getProvider()->isBlacklisted('kaboom'));

        $this->assertRegExp('/Successfully removed 2 password\(s\) from your blacklist database/', $commandTester->getDisplay());
    }

    public function testNoInput()
    {
        $application = new Application();
        $command = new BlacklistDeleteCommand();
        $command->setContainer(self::$container);
        $application->add($command);

        $command = $application->find('rollerworks-password:blacklist:delete');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertNotRegExp('/Successfully removed \d+ password\(s\) from your blacklist database/', $commandTester->getDisplay());
        $this->assertRegExp('/No passwords or file-option given/', $commandTester->getDisplay());
    }

    public function testReadFromFile()
    {
        $application = new Application();
        $command = new BlacklistDeleteCommand();
        $command->setContainer(self::$container);
        $application->add($command);

        $command = $application->find('rollerworks-password:blacklist:delete');

        $this->getProvider()->add('test');
        $this->getProvider()->add('foobar');
        $this->getProvider()->add('kaboom');

        $commandTester = new CommandTester($command);

        $commandTester->execute(array('command' => $command->getName(), '--file' => __DIR__.'/../fixtures/passwords-list1.txt'));
        $this->assertRegExp('/Successfully removed 2 password\(s\) from your blacklist database/', $commandTester->getDisplay());

        $this->assertFalse($this->getProvider()->isBlacklisted('test'));
        $this->assertFalse($this->getProvider()->isBlacklisted('foobar'));
        $this->assertTrue($this->getProvider()->isBlacklisted('kaboom'));
    }

    public function testImportExistingFromFile()
    {
        $application = new Application();
        $command = new BlacklistDeleteCommand();
        $command->setContainer(self::$container);
        $application->add($command);

        $command = $application->find('rollerworks-password:blacklist:delete');

        $this->getProvider()->add('test');
        $this->getProvider()->add('foobar');
        $this->getProvider()->add('kaboom');

        $commandTester = new CommandTester($command);

        $commandTester->execute(array('command' => $command->getName(), '--file' => __DIR__.'/../fixtures/passwords-list1.txt'));
        $this->assertRegExp('/Successfully removed 2 password\(s\) from your blacklist database/', $commandTester->getDisplay());

        $this->assertFalse($this->getProvider()->isBlacklisted('test'));
        $this->assertFalse($this->getProvider()->isBlacklisted('foobar'));
        $this->assertTrue($this->getProvider()->isBlacklisted('kaboom'));

        $commandTester->execute(array('command' => $command->getName(), '--file' => __DIR__.'/../fixtures/passwords-list1.txt'));
        $this->assertRegExp('/Successfully removed 0 password\(s\) from your blacklist database/', $commandTester->getDisplay());
    }

    public function testImportFromRelFile()
    {
        $application = new Application();
        $command = new BlacklistDeleteCommand();
        $command->setContainer(self::$container);
        $application->add($command);

        $command = $application->find('rollerworks-password:blacklist:delete');

        $this->getProvider()->add('test');
        $this->getProvider()->add('foobar');
        $this->getProvider()->add('kaboom');

        $commandTester = new CommandTester($command);

        // This changes the current working directory to this one so we can check relative files
        chdir(__DIR__);

        $commandTester->execute(array('command' => $command->getName(), '--file' => '../fixtures/passwords-list1.txt'));
        $this->assertRegExp('/Successfully removed 2 password\(s\) from your blacklist database/', $commandTester->getDisplay());

        $this->assertFalse($this->getProvider()->isBlacklisted('test'));
        $this->assertFalse($this->getProvider()->isBlacklisted('foobar'));
        $this->assertTrue($this->getProvider()->isBlacklisted('kaboom'));
    }

    public function testImportFromNoFile()
    {
        $application = new Application();
        $command = new BlacklistDeleteCommand();
        $command->setContainer(self::$container);
        $application->add($command);

        $command = $application->find('rollerworks-password:blacklist:delete');

        $this->assertFalse($this->getProvider()->isBlacklisted('test'));
        $this->assertFalse($this->getProvider()->isBlacklisted('foobar'));

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array('command' => $command->getName(), '--file' => '../fixtures/unknown.txt')
        );

        $this->assertRegExp('#Unable to read passwords list. No such file: \.\./fixtures/unknown\.txt#', $commandTester->getDisplay());
    }

    public function testImportFromEmptyFile()
    {
        $application = new Application();
        $command = new BlacklistDeleteCommand();
        $command->setContainer(self::$container);
        $application->add($command);

        $command = $application->find('rollerworks-password:blacklist:delete');

        $this->assertFalse($this->getProvider()->isBlacklisted('test'));
        $this->assertFalse($this->getProvider()->isBlacklisted('foobar'));

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array('command' => $command->getName(), '--file' => __DIR__.'/../fixtures/passwords-list2.txt')
        );

        $this->assertRegExp('/Passwords list seems empty, are you sure this is the correct file\?/', $commandTester->getDisplay());
    }
}
