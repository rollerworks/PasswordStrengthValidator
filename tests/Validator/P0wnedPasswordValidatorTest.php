<?php

/*
 * This file is part of the RollerworksPasswordStrengthValidator package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\PasswordStrength\Tests\Validator;

use PHPUnit\Framework\MockObject\MockObject;
use Rollerworks\Component\PasswordStrength\P0wnedPassword\Request\Client;
use Rollerworks\Component\PasswordStrength\P0wnedPassword\Request\Result;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\P0wnedPassword;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\P0wnedPasswordValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @group legacy
 */
class P0wnedPasswordValidatorTest extends ConstraintValidatorTestCase
{
    /** @var Client|MockObject */
    private $client;

    /** @var P0wnedPasswordValidator */
    protected $validator;

    protected function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
        parent::setUp();
        $this->constraint = new P0wnedPassword();
    }

    protected function createValidator()
    {
        return new P0wnedPasswordValidator($this->client);
    }

    public function testFound()
    {
        $result = new Result(4031);
        $this->client
            ->expects($this->once())
            ->method('check')
            ->with('correcthorsebattery')
            ->willReturn($result);

        $this->validator->validate('correcthorsebattery', $this->constraint);
        $this->assertCount(1, $this->context->getViolations());
    }

    public function testNotFound()
    {
        $result = new Result(0);
        $this->client
            ->expects($this->once())
            ->method('check')
            ->with('correcthorsebattery')
            ->willReturn($result);

        $this->validator->validate('correcthorsebattery', $this->constraint);

        $this->assertNoViolation();
    }
}
