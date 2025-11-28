<?php

declare(strict_types=1);

/*
 * This file is part of the RollerworksPasswordStrengthValidator package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\PasswordStrength\Tests\Validator;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordRequirements;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordRequirementsValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Symfony\Component\Validator\Test\ConstraintViolationAssertion;

/**
 * @internal
 *
 * @template-extends ConstraintValidatorTestCase<PasswordRequirementsValidator>
 */
final class PasswordRequirementsValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new PasswordRequirementsValidator();
    }

    #[Test]
    public function null_is_valid(): void
    {
        $this->validator->validate(null, new PasswordRequirements());

        $this->assertNoViolation();
    }

    #[Test]
    public function empty_is_valid(): void
    {
        $this->validator->validate('', new PasswordRequirements());

        $this->assertNoViolation();
    }

    #[Test]
    #[DataProvider('provideValid_value_constraintsCases')]
    public function valid_value_constraints(string $value, PasswordRequirements $constraint): void
    {
        $this->value = $value;

        $this->validator->validate($value, $constraint);

        $this->assertNoViolation();
    }

    /**
     * @return iterable<int, array{0: string, 1: PasswordRequirements}>
     */
    public static function provideValid_value_constraintsCases(): iterable
    {
        return [
            ['test', new PasswordRequirements(minLength: 3)],
            ['1234567', new PasswordRequirements(requireLetters: false)],
            ['1234567', new PasswordRequirements(requireLetters: false)],
            ['aBcDez', new PasswordRequirements(requireCaseDiff: true)],
            ['abcdef', new PasswordRequirements(requireNumbers: false)],
            ['123456', new PasswordRequirements(requireLetters: false, requireNumbers: true)],
            ['１２３４５６７８９', new PasswordRequirements(requireLetters: false, requireNumbers: true)],
            ['abcd12345', new PasswordRequirements(requireLetters: true, requireNumbers: true)],
            ['１２３４abc５６７８９', new PasswordRequirements(requireLetters: true, requireNumbers: true)],

            ['®', new PasswordRequirements(minLength: 1, requireLetters: false, requireSpecialCharacter: true)],
            ['»', new PasswordRequirements(minLength: 1, requireLetters: false, requireSpecialCharacter: true)],
            ['<>', new PasswordRequirements(minLength: 1, requireLetters: false, requireSpecialCharacter: true)],
            ['{}', new PasswordRequirements(minLength: 1, requireLetters: false, requireSpecialCharacter: true)],
        ];
    }

    /**
     * @param array<int, array{0: string, 1: PasswordRequirements, 2: array<array-key, mixed>}> $violations
     */
    #[Test]
    #[DataProvider('provideViolation_value_constraintsCases')]
    public function violation_value_constraints(string $value, PasswordRequirements $constraint, array $violations = []): void
    {
        $this->value = $value;
        /** @var ConstraintViolationAssertion $constraintViolationAssertion */
        $constraintViolationAssertion = null; // Shut-up PHPStan

        $this->validator->validate($value, $constraint);

        /**
         * @var array<int, mixed> $violation
         */
        foreach ($violations as $i => $violation) {
            if ($i === 0) {
                $constraintViolationAssertion = $this->buildViolation($violation[0])
                    ->setParameters($violation[1] ?? [])
                    ->setInvalidValue($value)
                ;
            } else {
                $constraintViolationAssertion = $constraintViolationAssertion->buildNextViolation($violation[0])
                    ->setParameters($violation[1] ?? [])
                    ->setInvalidValue($value)
                ;
            }

            if ($i == \count($violations) - 1) {
                $constraintViolationAssertion->assertRaised();
            }
        }
    }

    /**
     * @return iterable<int, array{0: string, 1: PasswordRequirements, 2: array<array-key, mixed>}>
     */
    public static function provideViolation_value_constraintsCases(): iterable
    {
        $constraint = new PasswordRequirements();

        return [
            ['１', new PasswordRequirements(minLength: 2, requireLetters: false), [
                [$constraint->tooShortMessage, ['{{length}}' => 2]],
            ]],
            ['test', new PasswordRequirements(requireLetters: true), [
                [$constraint->tooShortMessage, ['{{length}}' => $constraint->minLength]],
            ]],
            ['123456', new PasswordRequirements(requireLetters: true), [
                [$constraint->missingLettersMessage],
            ]],
            ['abcdez', new PasswordRequirements(requireCaseDiff: true), [
                [$constraint->requireCaseDiffMessage],
            ]],
            ['!@#$%^&*()-', new PasswordRequirements(requireLetters: true, requireNumbers: true), [
                [$constraint->missingLettersMessage],
                [$constraint->missingNumbersMessage],
            ]],
            ['aerfghy', new PasswordRequirements(requireLetters: false, requireSpecialCharacter: true), [
                [$constraint->missingSpecialCharacterMessage],
            ]],
        ];
    }
}
