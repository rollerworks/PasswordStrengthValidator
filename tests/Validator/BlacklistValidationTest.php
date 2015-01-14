<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\PasswordStrengthBundle\Tests\Validator;

use Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints\Blacklist;
use Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints\BlacklistValidator;
use Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\ArrayProvider;

class BlacklistValidationTest extends \PHPUnit_Framework_TestCase
{
    protected $walker;

    /**
     * @var \Symfony\Component\Validator\ExecutionContext|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var BlacklistValidator
     */
    protected $validator;

    protected function setUp()
    {
        $provider = new ArrayProvider(array('test', 'foobar'));

        $this->context = $this->getMock('Symfony\Component\Validator\ExecutionContext', array(), array(), '', false);
        $this->validator = new BlacklistValidator($provider);
        $this->validator->initialize($this->context);
    }

    protected function tearDown()
    {
        $this->validator = null;
        $this->context = null;
    }

    public function testNullIsValid()
    {
        $this->context->expects($this->never())
            ->method('addViolation')
        ;

        $this->validator->validate(null, new Blacklist());
    }

    public function testEmptyStringIsValid()
    {
        $this->context->expects($this->never())
            ->method('addViolation')
        ;

        $this->validator->validate('', new Blacklist());
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
        $this->context->expects($this->never())
            ->method('addViolation')
        ;

        $constraint = new Blacklist();
        $this->validator->validate('weak', $constraint);
        $this->validator->validate('tests', $constraint);
    }

    public function testBlackListed()
    {
        $this->context->expects($this->once())
            ->method('addViolation')
        ;

        $constraint = new Blacklist();
        $this->validator->validate('test', $constraint);
    }
}
