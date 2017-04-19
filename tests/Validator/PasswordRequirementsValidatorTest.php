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

use Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints\PasswordRequirements;
use Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints\PasswordRequirementsValidator;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;
use Symfony\Component\Validator\Validation;

class PasswordRequirementsValidatorTest extends AbstractConstraintValidatorTest
{
    public function getMock($originalClassName, $methods = array(), array $arguments = array(), $mockClassName = '', $callOriginalConstructor = true, $callOriginalClone = true, $callAutoload = true, $cloneArguments = false, $callOriginalMethods = false, $proxyTarget = null)
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

    protected function getApiVersion()
    {
        return Validation::API_VERSION_2_5;
    }

    protected function createValidator()
    {
        return new PasswordRequirementsValidator();
    }

    public function testNullIsValid()
    {
        $this->validator->validate(null, new PasswordRequirements());

        $this->assertNoViolation();
    }

    public function testEmptyIsValid()
    {
        $this->validator->validate('', new PasswordRequirements());

        $this->assertNoViolation();
    }

    /**
     * @dataProvider provideValidConstraints
     *
     * @param string               $value
     * @param PasswordRequirements $constraint
     */
    public function testValidValueConstraints($value, PasswordRequirements $constraint)
    {
        $this->value = $value;

        $this->validator->validate($value, $constraint);

        $this->assertNoViolation();
    }

    /**
     * @dataProvider provideViolationConstraints
     *
     * @param string               $value
     * @param PasswordRequirements $constraint
     * @param array                $violations
     */
    public function testViolationValueConstraints($value, PasswordRequirements $constraint, array $violations = array())
    {
        $this->value = $value;

        $this->validator->validate($value, $constraint);

        foreach ($violations as $i => $violation) {
            if ($i == 0) {
                $constraintViolationAssertion = $this->buildViolation($violation[0])
                    ->setParameters(isset($violation[1]) ? $violation[1] : array())
                    ->setInvalidValue($value);
            } else {
                $constraintViolationAssertion = $constraintViolationAssertion->buildNextViolation($violation[0])
                    ->setParameters(isset($violation[1]) ? $violation[1] : array())
                    ->setInvalidValue($value);
            }
            if ($i == count($violations) - 1) {
                $constraintViolationAssertion->assertRaised();
            }
        }
    }

    public function provideValidConstraints()
    {
        return array(
            array('test', new PasswordRequirements(array('minLength' => 3))),
            array('1234567', new PasswordRequirements(array('requireLetters' => false))),
            array('1234567', new PasswordRequirements(array('requireLetters' => false))),
            array('aBcDez', new PasswordRequirements(array('requireCaseDiff' => true))),
            array('abcdef', new PasswordRequirements(array('requireNumbers' => false))),
            array('123456', new PasswordRequirements(array('requireLetters' => false, 'requireNumbers' => true))),
            array('１２３４５６７８９', new PasswordRequirements(array('requireLetters' => false, 'requireNumbers' => true))),
            array('abcd12345', new PasswordRequirements(array('requireLetters' => true, 'requireNumbers' => true))),
            array('１２３４abc５６７８９', new PasswordRequirements(array('requireLetters' => true, 'requireNumbers' => true))),

            array('®', new PasswordRequirements(array('minLength' => 1, 'requireLetters' => false, 'requireSpecialCharacter' => true))),
            array('»', new PasswordRequirements(array('minLength' => 1, 'requireLetters' => false, 'requireSpecialCharacter' => true))),
            array('<>', new PasswordRequirements(array('minLength' => 1, 'requireLetters' => false, 'requireSpecialCharacter' => true))),
        );
    }

    public function provideViolationConstraints()
    {
        $constraint = new PasswordRequirements();

        return array(
            array('１', new PasswordRequirements(array('minLength' => 2, 'requireLetters' => false)), array(
                array($constraint->tooShortMessage, array('{{length}}' => 2)),
            )),
            array('test', new PasswordRequirements(array('requireLetters' => true)), array(
                array($constraint->tooShortMessage, array('{{length}}' => $constraint->minLength)),
            )),
            array('123456', new PasswordRequirements(array('requireLetters' => true)), array(
                array($constraint->missingLettersMessage),
            )),
            array('abcdez', new PasswordRequirements(array('requireCaseDiff' => true)), array(
                array($constraint->requireCaseDiffMessage),
            )),
            array('!@#$%^&*()-', new PasswordRequirements(array('requireLetters' => true, 'requireNumbers' => true)), array(
                array($constraint->missingLettersMessage),
                array($constraint->missingNumbersMessage),
            )),
            array('aerfghy', new PasswordRequirements(array('requireLetters' => false, 'requireSpecialCharacter' => true)), array(
                array($constraint->missingSpecialCharacterMessage),
            )),
        );
    }
}
