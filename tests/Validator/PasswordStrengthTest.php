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

use Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints\PasswordStrength;
use Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints\PasswordStrengthValidator;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;
use Symfony\Component\Validator\Validation;

class PasswordStrengthTest extends AbstractConstraintValidatorTest
{
    protected function getApiVersion()
    {
        return Validation::API_VERSION_2_5;
    }

    protected function createValidator()
    {
        return new PasswordStrengthValidator();
    }

    public function testNullIsValid()
    {
        $this->validator->validate(null, new PasswordStrength(6));

        $this->assertNoViolation();
    }

    public function testEmptyIsValid()
    {
        $this->validator->validate('', new PasswordStrength(6));

        $this->assertNoViolation();
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
            array('weaker'),
            array('123456'),
            array('foobar'),
            array('!.!.!.'),
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
            array('fjsfjdljfsjsjjls1'),
            array('785737592375294b'),
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
        $constraint = new PasswordStrength(2);

        $this->validator->validate($value, $constraint);

        $this->buildViolation('password_too_weak')
            ->setParameters(array('{{ length }}' => 6))
            ->assertRaised();
    }

    /**
     * @dataProvider getWeakPasswords
     */
    public function testWeakPasswords($value)
    {
        $constraint = new PasswordStrength(array('minStrength' => 3, 'minLength' => 7));

        $this->validator->validate($value, $constraint);

        $this->buildViolation('password_too_weak')
            ->setParameters(array('{{ length }}' => 7))
            ->assertRaised();
    }

    /**
     * @dataProvider getMediumPasswords
     */
    public function testMediumPasswords($value)
    {
        $constraint = new PasswordStrength(4);

        $this->validator->validate($value, $constraint);

        $this->buildViolation('password_too_weak')
            ->setParameters(array('{{ length }}' => 6))
            ->assertRaised();
    }

    /**
     * @dataProvider getStrongPasswords
     */
    public function testStrongPasswords($value)
    {
        $constraint = new PasswordStrength(5);

        $this->validator->validate($value, $constraint);

        $this->buildViolation('password_too_weak')
            ->setParameters(array('{{ length }}' => 6))
            ->assertRaised();
    }

    /**
     * @dataProvider getVeryStrongPasswords
     */
    public function testVeryStrongPasswords($value)
    {
        $constraint = new PasswordStrength(5);

        $this->validator->validate($value, $constraint);

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getVeryWeakPasswords
     */
    public function testVeryWeakPasswordWillNotPass($value)
    {
        $constraint = new PasswordStrength(2);

        $this->validator->validate($value, $constraint);

        $this->buildViolation('password_too_weak')
            ->setParameters(array('{{ length }}' => 6))
            ->assertRaised();
    }

    /**
     * @dataProvider getWeakPasswords
     */
    public function testWeakPasswordsWillNotPass($value)
    {
        $constraint = new PasswordStrength(3);

        $this->validator->validate($value, $constraint);

        $this->buildViolation('password_too_weak')
            ->setParameters(array('{{ length }}' => 6))
            ->assertRaised();
    }

    /**
     * @dataProvider getMediumPasswords
     */
    public function testMediumPasswordWillNotPass($value)
    {
        $constraint = new PasswordStrength(4);

        $this->validator->validate($value, $constraint);

        $this->buildViolation('password_too_weak')
            ->setParameters(array('{{ length }}' => 6))
            ->assertRaised();
    }

    /**
     * @dataProvider getStrongPasswords
     */
    public function testStrongPasswordWillNotPass($value)
    {
        $constraint = new PasswordStrength(5);

        $this->validator->validate($value, $constraint);

        $this->buildViolation('password_too_weak')
            ->setParameters(array('{{ length }}' => 6))
            ->assertRaised();
    }

    public function testConstraintGetDefaultOption()
    {
        $constraint = new PasswordStrength(5);

        $this->assertEquals(5, $constraint->minStrength);
    }
}
