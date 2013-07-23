<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rollerworks\Bundle\PasswordStrengthBundle\Tests\Validator;

use Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints\PasswordStrength;
use Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints\PasswordStrengthValidator;

class PasswordStrengthTest extends \PHPUnit_Framework_TestCase
{
    protected $walker;

    /**
     * @var \Symfony\Component\Validator\ExecutionContext|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var PasswordStrengthValidator|
     */
    protected $validator;

    protected function setUp()
    {
        $this->context = $this->getMock('Symfony\Component\Validator\ExecutionContext', array(), array(), '', false);
        $this->validator = new PasswordStrengthValidator();
        $this->validator->initialize($this->context);
    }

    protected function tearDown()
    {
        $this->validator = null;
        $this->context = null;
    }

    public function testNullIsValid()
    {
        $this->context->expects($this->once())
            ->method('addViolation');

        $this->validator->validate(null, new PasswordStrength(6));
    }

    public function testEmptyStringIsValid()
    {
        $this->context->expects($this->once())
            ->method('addViolation');

        $this->validator->validate('', new PasswordStrength(6));
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testExpectsStringCompatibleType()
    {
        $this->validator->validate(new \stdClass(), new PasswordStrength(5));
    }

    public static function getVeryWeakPasswords()
    {
        return array(
            array('weak'),
            array('foo'),
            array('123456'),
            array('foobar'),
            array('foobar'),
        );
    }

    public static function getWeakPasswords()
    {
        return array(
            array('wee6eak'),
            array('foobar!'),
            array('Foobar'),
            array('123456!'),
            array('7857375923752947'),
            array('fjsfjdljfsjsjjlsj'),
        );
    }

    public static function getMediumPasswords()
    {
        return array(
            array('Foobar!'),
            array('foo-b0r!'),
        );
    }

    public static function getStrongPasswords()
    {
        return array(
            array('Foobar!55!'),
            array('Foobar$55'),
            array('Foobar€55'),
            array('Foobar€55'),
        );
    }

    public static function getVeryStrongPasswords()
    {
        return array(
            array('Foobar$55_4&F'),
            array('L33RoyJ3Jenkins!'),
        );
    }

    /**
     * @dataProvider getVeryWeakPasswords
     */
    public function testVeryWeakPasswords($value)
    {
        $this->context->expects($this->once())
            ->method('addViolation');

        $constraint = new PasswordStrength(2);
        $this->validator->validate($value, $constraint);
    }

    /**
     * @dataProvider getWeakPasswords
     */
    public function testWeakPasswords($value)
    {
        $this->context->expects($this->once())
            ->method('addViolation');

        $constraint = new PasswordStrength(3);
        $this->validator->validate($value, $constraint);
    }

    /**
     * @dataProvider getMediumPasswords
     */
    public function testMediumPasswords($value)
    {
        $this->context->expects($this->once())
            ->method('addViolation');

        $constraint = new PasswordStrength(4);
        $this->validator->validate($value, $constraint);
    }

    /**
     * @dataProvider getStrongPasswords
     */
    public function testStrongPasswords($value)
    {
        $this->context->expects($this->once())
            ->method('addViolation');

        $constraint = new PasswordStrength(5);
        $this->validator->validate($value, $constraint);
    }

    /**
     * @dataProvider getVeryStrongPasswords
     */
    public function testVeryStrongPasswords($value)
    {
        $this->context->expects($this->never())
            ->method('addViolation');

        $constraint = new PasswordStrength(5);
        $this->validator->validate($value, $constraint);
    }

    /**
     * @dataProvider getVeryWeakPasswords
     */
    public function testVeryWeakPasswordsValid($value)
    {
        $this->context->expects($this->once())
            ->method('addViolation');

        $constraint = new PasswordStrength(1);
        $this->validator->validate($value, $constraint);
    }

    /**
     * @dataProvider getWeakPasswords
     */
    public function testWeakPasswordsValid($value)
    {
        $this->context->expects($this->once())
            ->method('addViolation');

        $constraint = new PasswordStrength(array(2));
        $this->validator->validate($value, $constraint);
    }

    /**
     * @dataProvider getMediumPasswords
     */
    public function testMediumPasswordValid($value)
    {
        $this->context->expects($this->once())
            ->method('addViolation');

        $constraint = new PasswordStrength(array(3));
        $this->validator->validate($value, $constraint);
    }

    /**
     * @dataProvider getStrongPasswords
     */
    public function testStrongPasswordsValid($value)
    {
        $this->context->expects($this->once())
            ->method('addViolation');

        $constraint = new PasswordStrength(array(4));
        $this->validator->validate($value, $constraint);
    }

    public function testConstraintGetDefaultOption()
    {
        $constraint = new PasswordStrength(5);

        $this->assertEquals(5, $constraint->minStrength);
    }
}
