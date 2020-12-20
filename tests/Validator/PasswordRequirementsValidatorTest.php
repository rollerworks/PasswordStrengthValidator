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

use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordRequirements;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordRequirementsValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Symfony\Component\Validator\Test\ConstraintViolationAssertion;

class PasswordRequirementsValidatorTest extends ConstraintValidatorTestCase
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
     * @param string $value
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
     * @param string $value
     */
    public function testViolationValueConstraints($value, PasswordRequirements $constraint, array $violations = [])
    {
        $this->value = $value;
        /** @var ConstraintViolationAssertion $constraintViolationAssertion */
        $constraintViolationAssertion = null; // Shut-up PHPStan

        $this->validator->validate($value, $constraint);

        foreach ($violations as $i => $violation) {
            if ($i === 0) {
                $constraintViolationAssertion = $this->buildViolation($violation[0])
                    ->setParameters(isset($violation[1]) ? $violation[1] : [])
                    ->setInvalidValue($value);
            } else {
                $constraintViolationAssertion = $constraintViolationAssertion->buildNextViolation($violation[0])
                    ->setParameters(isset($violation[1]) ? $violation[1] : [])
                    ->setInvalidValue($value);
            }
            if ($i == count($violations) - 1) {
                $constraintViolationAssertion->assertRaised();
            }
        }
    }

    public function provideValidConstraints()
    {
        return [
            ['test', new PasswordRequirements(['minLength' => 3])],
            ['1234567', new PasswordRequirements(['requireLetters' => false])],
            ['1234567', new PasswordRequirements(['requireLetters' => false])],
            ['aBcDez', new PasswordRequirements(['requireCaseDiff' => true])],
            ['abcdef', new PasswordRequirements(['requireNumbers' => false])],
            ['123456', new PasswordRequirements(['requireLetters' => false, 'requireNumbers' => true])],
            ['１２３４５６７８９', new PasswordRequirements(['requireLetters' => false, 'requireNumbers' => true])],
            ['abcd12345', new PasswordRequirements(['requireLetters' => true, 'requireNumbers' => true])],
            ['１２３４abc５６７８９', new PasswordRequirements(['requireLetters' => true, 'requireNumbers' => true])],

            ['®', new PasswordRequirements(['minLength' => 1, 'requireLetters' => false, 'requireSpecialCharacter' => true])],
            ['»', new PasswordRequirements(['minLength' => 1, 'requireLetters' => false, 'requireSpecialCharacter' => true])],
            ['<>', new PasswordRequirements(['minLength' => 1, 'requireLetters' => false, 'requireSpecialCharacter' => true])],
            ['{}', new PasswordRequirements(['minLength' => 1, 'requireLetters' => false, 'requireSpecialCharacter' => true])],
        ];
    }

    public function provideViolationConstraints()
    {
        $constraint = new PasswordRequirements();

        return [
            ['１', new PasswordRequirements(['minLength' => 2, 'requireLetters' => false]), [
                [$constraint->tooShortMessage, ['{{length}}' => 2]],
            ]],
            ['test', new PasswordRequirements(['requireLetters' => true]), [
                [$constraint->tooShortMessage, ['{{length}}' => $constraint->minLength]],
            ]],
            ['123456', new PasswordRequirements(['requireLetters' => true]), [
                [$constraint->missingLettersMessage],
            ]],
            ['abcdez', new PasswordRequirements(['requireCaseDiff' => true]), [
                [$constraint->requireCaseDiffMessage],
            ]],
            ['!@#$%^&*()-', new PasswordRequirements(['requireLetters' => true, 'requireNumbers' => true]), [
                [$constraint->missingLettersMessage],
                [$constraint->missingNumbersMessage],
            ]],
            ['aerfghy', new PasswordRequirements(['requireLetters' => false, 'requireSpecialCharacter' => true]), [
                [$constraint->missingSpecialCharacterMessage],
            ]],
        ];
    }
}
