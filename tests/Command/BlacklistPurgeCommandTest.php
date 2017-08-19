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

use Rollerworks\Component\PasswordStrength\Command\BlacklistPurgeCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
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

        self::assertTrue($this->getProvider()->isBlacklisted('test'));
        self::assertTrue($this->getProvider()->isBlacklisted('foobar'));
        self::assertTrue($this->getProvider()->isBlacklisted('kaboom'));

        $commandTester = new CommandTester($command);

        // Symfony <2.5 BC
        /** @var QuestionHelper|DialogHelper $questionHelper */
        $questionHelper = $command->getHelperSet()->has('question') ? $command->getHelperSet()->get('question') : $command->getHelperSet()->get('dialog');

        if (method_exists($commandTester, 'setInputs')) {
            $commandTester->setInputs(array('no'));
        } else {
            $questionHelper->setInputStream($this->getInputStream("n\nno\n"));
        }

        $commandTester->execute(array('command' => $command->getName()), array('interactive' => true));

        self::assertRegExp('/This will remove all the passwords from your blacklist database!!/', $commandTester->getDisplay());

        self::assertTrue($this->getProvider()->isBlacklisted('test'));
        self::assertTrue($this->getProvider()->isBlacklisted('foobar'));
        self::assertTrue($this->getProvider()->isBlacklisted('kaboom'));
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

        self::assertTrue($this->getProvider()->isBlacklisted('test'));
        self::assertTrue($this->getProvider()->isBlacklisted('foobar'));
        self::assertTrue($this->getProvider()->isBlacklisted('kaboom'));

        $commandTester = new CommandTester($command);

        // Symfony <2.5 BC
        /** @var QuestionHelper|DialogHelper $questionHelper */
        $questionHelper = $command->getHelperSet()->has('question') ? $command->getHelperSet()->get('question') : $command->getHelperSet()->get('dialog');

        if (method_exists($commandTester, 'setInputs')) {
            $commandTester->setInputs(array('y', 'yes'));
        } else {
            $questionHelper->setInputStream($this->getInputStream("y\nyes\n"));
        }

        $commandTester->execute(array('command' => $command->getName()));

        self::assertRegExp('/This will remove all the passwords from your blacklist database!!/', $commandTester->getDisplay());

        self::assertFalse($this->getProvider()->isBlacklisted('test'));
        self::assertFalse($this->getProvider()->isBlacklisted('foobar'));
        self::assertFalse($this->getProvider()->isBlacklisted('kaboom'));
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

        self::assertTrue($this->getProvider()->isBlacklisted('test'));
        self::assertTrue($this->getProvider()->isBlacklisted('foobar'));
        self::assertTrue($this->getProvider()->isBlacklisted('kaboom'));

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '--no-ask' => null));

        self::assertFalse($this->getProvider()->isBlacklisted('test'));
        self::assertFalse($this->getProvider()->isBlacklisted('foobar'));
        self::assertFalse($this->getProvider()->isBlacklisted('kaboom'));
    }

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fwrite($stream, $input);
        rewind($stream);

        return $stream;
    }
}
