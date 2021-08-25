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

use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordStrength;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordStrengthValidator;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
final class PasswordStrengthTest extends ConstraintValidatorTestCase
{
    /**
     * @var array
     */
    private static $levelToLabel = [
        1 => 'very_weak',
        2 => 'weak',
        3 => 'medium',
        4 => 'strong',
        5 => 'very_strong',
    ];

    protected function createValidator()
    {
        return new PasswordStrengthValidator(new Translator('en'));
    }

    /**
     * @test
     */
    public function null_is_valid()
    {
        $this->validator->validate(null, new PasswordStrength(6));

        $this->assertNoViolation();
    }

    /**
     * @test
     */
    public function empty_is_valid()
    {
        $this->validator->validate('', new PasswordStrength(6));

        $this->assertNoViolation();
    }

    /**
     * @test
     */
    public function expects_string_compatible_type()
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate(new \stdClass(), new PasswordStrength(5));
    }

    public function getWeakPasswords()
    {
        $pre = 'rollerworks_password.tip.';

        return [
            // Very weak
            [2, 'weaker', 1, "{$pre}uppercase_letters, {$pre}numbers, {$pre}special_chars, {$pre}length"],
            [2, '123456', 1, "{$pre}letters, {$pre}special_chars, {$pre}length"],
            [2, 'foobar', 1, "{$pre}uppercase_letters, {$pre}numbers, {$pre}special_chars, {$pre}length"],
            [2, '!.!.!.', 1, "{$pre}letters, {$pre}numbers, {$pre}length"],

            // Weak
            [3, 'wee6eak', 2, "{$pre}uppercase_letters, {$pre}special_chars, {$pre}length"],
            [3, 'foobar!', 2, "{$pre}uppercase_letters, {$pre}numbers, {$pre}length"],
            [3, 'Foobar', 2, "{$pre}numbers, {$pre}special_chars, {$pre}length"],
            [3, '123456!', 2, "{$pre}letters, {$pre}length"],
            [3, '7857375923752947', 2, "{$pre}letters, {$pre}special_chars"],
            [3, 'FSDFJSLKFFSDFDSF', 2, "{$pre}lowercase_letters, {$pre}numbers, {$pre}special_chars"],
            [3, 'fjsfjdljfsjsjjlsj', 2, "{$pre}uppercase_letters, {$pre}numbers, {$pre}special_chars"],

            // Medium
            [4, 'Foobar!', 3, "{$pre}numbers, {$pre}length"],
            [4, 'foo-b0r!', 3, "{$pre}uppercase_letters, {$pre}length"],
            [4, 'fjsfjdljfsjsjjls1', 3, "{$pre}uppercase_letters, {$pre}special_chars"],
            [4, '785737592375294b', 3, "{$pre}uppercase_letters, {$pre}special_chars"],
        ];
    }

    public function getWeakPasswordsUnicode()
    {
        $pre = 'rollerworks_password.tip.';

        // \u{FD3E} = ﴾ = Arabic ornate left parenthesis

        return [
            // Very weak
            [2, 'weaker', 1, "{$pre}uppercase_letters, {$pre}numbers, {$pre}special_chars, {$pre}length"],
            [2, '123456', 1, "{$pre}letters, {$pre}special_chars, {$pre}length"],
            [2, '²²²²²²', 1, "{$pre}letters, {$pre}special_chars, {$pre}length"],
            [2, 'foobar', 1, "{$pre}uppercase_letters, {$pre}numbers, {$pre}special_chars, {$pre}length"],
            [2, 'ömgwat', 1, "{$pre}uppercase_letters, {$pre}numbers, {$pre}special_chars, {$pre}length"],
            [2, '!.!.!.', 1, "{$pre}letters, {$pre}numbers, {$pre}length"],
            [2, '!.!.!﴾', 1, "{$pre}letters, {$pre}numbers, {$pre}length"],

            // Weak
            [3, 'wee6eak', 2, "{$pre}uppercase_letters, {$pre}special_chars, {$pre}length"],
            [3, 'foobar!', 2, "{$pre}uppercase_letters, {$pre}numbers, {$pre}length"],
            [3, 'Foobar', 2, "{$pre}numbers, {$pre}special_chars, {$pre}length"],
            [3, '123456!', 2, "{$pre}letters, {$pre}length"],
            [3, '7857375923752947', 2, "{$pre}letters, {$pre}special_chars"],
            [3, 'FSDFJSLKFFSDFDSF', 2, "{$pre}lowercase_letters, {$pre}numbers, {$pre}special_chars"],
            [3, 'FÜKFJSLKFFSDFDSF', 2, "{$pre}lowercase_letters, {$pre}numbers, {$pre}special_chars"],
            [3, 'fjsfjdljfsjsjjlsj', 2, "{$pre}uppercase_letters, {$pre}numbers, {$pre}special_chars"],

            // Medium
            [4, 'Foobar﴾', 3, "{$pre}numbers, {$pre}length"],
            [4, 'foo-b0r!', 3, "{$pre}uppercase_letters, {$pre}length"],
            [4, 'fjsfjdljfsjsjjls1', 3, "{$pre}uppercase_letters, {$pre}special_chars"],
            [4, '785737592375294b', 3, "{$pre}uppercase_letters, {$pre}special_chars"],
        ];
    }

    public static function getStrongPasswords()
    {
        return [
            ['Foobar!55!'],
            ['Foobar$55'],
            ['Foobar€55'],
            ['Foobar€55'],
        ];
    }

    public static function getVeryStrongPasswords()
    {
        return [
            ['Foobar$55_4&F'],
            ['L33RoyJ3Jenkins!'],
        ];
    }

    /**
     * @test
     */
    public function short_password_will_not_pass()
    {
        $constraint = new PasswordStrength(['minStrength' => 5, 'minLength' => 6]);

        $this->validator->validate('foo', $constraint);

        $parameters = [
            '{{length}}' => 6,
        ];

        $this->buildViolation('Your password must be at least {{length}} characters long.')
            ->setParameters($parameters)
            ->assertRaised()
        ;
    }

    /**
     * @test
     */
    public function short_password_in_multi_byte_will_not_pass()
    {
        $constraint = new PasswordStrength(['minStrength' => 5, 'minLength' => 7]);

        $this->validator->validate('foöled', $constraint);

        $parameters = [
            '{{length}}' => 7,
        ];

        $this->buildViolation('Your password must be at least {{length}} characters long.')
            ->setParameters($parameters)
            ->assertRaised()
        ;
    }

    /**
     * @dataProvider getWeakPasswords
     *
     * @test
     */
    public function weak_passwords_will_not_pass($minStrength, $value, $currentStrength, $tips = '')
    {
        $constraint = new PasswordStrength(['minStrength' => $minStrength, 'minLength' => 6]);

        $this->validator->validate($value, $constraint);

        $parameters = [
            '{{ length }}' => 6,
            '{{ min_strength }}' => 'rollerworks_password.strength_level.' . self::$levelToLabel[$minStrength],
            '{{ current_strength }}' => 'rollerworks_password.strength_level.' . self::$levelToLabel[$currentStrength],
            '{{ strength_tips }}' => $tips,
        ];

        $this->buildViolation('password_too_weak')
            ->setParameters($parameters)
            ->assertRaised()
        ;
    }

    /**
     * @dataProvider getWeakPasswordsUnicode
     *
     * @test
     */
    public function weak_passwords_with_unicode_will_not_pass($minStrength, $value, $currentStrength, $tips = '')
    {
        $constraint = new PasswordStrength(['minStrength' => $minStrength, 'minLength' => 6, 'unicodeEquality' => true]);

        $this->validator->validate($value, $constraint);

        $parameters = [
            '{{ length }}' => 6,
            '{{ min_strength }}' => 'rollerworks_password.strength_level.' . self::$levelToLabel[$minStrength],
            '{{ current_strength }}' => 'rollerworks_password.strength_level.' . self::$levelToLabel[$currentStrength],
            '{{ strength_tips }}' => $tips,
        ];

        $this->buildViolation('password_too_weak')
            ->setParameters($parameters)
            ->assertRaised()
        ;
    }

    /**
     * @dataProvider getVeryStrongPasswords
     *
     * @test
     */
    public function strong_passwords_will_pass($value)
    {
        $constraint = new PasswordStrength(5);

        $this->validator->validate($value, $constraint);

        $this->assertNoViolation();
    }

    /**
     * @test
     */
    public function constraint_get_default_option()
    {
        $constraint = new PasswordStrength(5);

        self::assertEquals(5, $constraint->minStrength);
    }

    /**
     * @test
     */
    public function parameters_are_translated_when_translator_is_missing()
    {
        $this->validator = new PasswordStrengthValidator();
        $this->validator->initialize($this->context);

        $constraint = new PasswordStrength(['minStrength' => 5, 'minLength' => 6]);

        $this->validator->validate('FD43f.!', $constraint);

        $parameters = [
            '{{ length }}' => 6,
            '{{ current_strength }}' => 'Strong',
            '{{ min_strength }}' => 'Very strong',
            '{{ strength_tips }}' => 'add more characters',
        ];

        $this->buildViolation('password_too_weak')
            ->setParameters($parameters)
            ->assertRaised()
        ;
    }
}
