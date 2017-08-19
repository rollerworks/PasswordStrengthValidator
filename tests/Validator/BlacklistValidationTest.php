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

use Rollerworks\Component\PasswordStrength\Blacklist\ArrayProvider;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\Blacklist;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\BlacklistValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class BlacklistValidationTest extends ConstraintValidatorTestCase
{
    public function getMock($originalClassName, $methods = [], array $arguments = [], $mockClassName = '', $callOriginalConstructor = true, $callOriginalClone = true, $callAutoload = true, $cloneArguments = false, $callOriginalMethods = false, $proxyTarget = null)
    {
        if (func_num_args() === 1 && preg_match('/^Symfony\\\\Component\\\\([a-z]+\\\\)+[a-z]+Interface$/i', $originalClassName)) {
            return $this->getMockBuilder($originalClassName)->getMock();
        }

        return parent::getMock(
            $originalClassName,
            $methods,
            $arguments,
            $mockClassName,
            $callOriginalConstructor,
            $callOriginalClone,
            $callAutoload,
            $cloneArguments,
            $callOriginalMethods,
            $proxyTarget
        );
    }

    protected function createValidator()
    {
        $provider = new ArrayProvider(['test', 'foobar']);

        return new BlacklistValidator($provider);
    }

    public function testNullIsValid()
    {
        $this->validator->validate(null, new Blacklist());

        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid()
    {
        $this->validator->validate('', new Blacklist());

        $this->assertNoViolation();
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testExpectsStringCompatibleType()
    {
        $this->validator->validate(new \stdClass(), new Blacklist());
    }

    public function testNotBlackListed()
    {
        $constraint = new Blacklist();
        $this->validator->validate('weak', $constraint);
        $this->validator->validate('tests', $constraint);

        $this->assertNoViolation();
    }

    public function testBlackListed()
    {
        $constraint = new Blacklist([
            'message' => 'myMessage',
        ]);
        $this->validator->validate('test', $constraint);

        $this->buildViolation('myMessage')
            ->setInvalidValue('test')
            ->assertRaised();
    }
}
