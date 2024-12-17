<?php

/*
 * This file is part of the RollerworksPasswordStrengthValidator package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\PasswordStrength\Validator\Constraints;

use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Password strength Validation.
 *
 * Validates if the password strength is equal or higher
 * to the required minimum and the password length is equal
 * or longer than the minimum length.
 *
 * The strength is computed from various measures including
 * length and usage of characters.
 *
 * The strengths are marked up as follow.
 *  1: Very Weak
 *  2: Weak
 *  3: Medium
 *  4: Strong
 *  5: Very Strong
 */
class PasswordStrengthValidator extends ConstraintValidator
{
    private TranslatorInterface $translator;

    /**
     * @var array<int, string>
     */
    private static array $levelToLabel = [
        1 => 'very_weak',
        2 => 'weak',
        3 => 'medium',
        4 => 'strong',
        5 => 'very_strong',
    ];

    public function __construct(?TranslatorInterface $translator = null)
    {
        // If translator is missing create a new translator.
        // With the 'en' locale and 'validators' domain.
        if ($translator === null) {
            $translator = new Translator('en');
            $translator->addLoader('xlf', new XliffFileLoader());
            $translator->addResource('xlf', \dirname(__DIR__, 2) . '/Resources/translations/validators.en.xlf', 'en', 'validators');
        }

        $this->translator = $translator;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (! $constraint instanceof PasswordStrength) {
            throw new UnexpectedTypeException($constraint, PasswordStrength::class);
        }

        if (! \is_scalar($value) && ! (\is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $value = (string) $value;
        $passLength = mb_strlen($value);

        if ($passLength < $constraint->minLength) {
            $this->context->buildViolation($constraint->tooShortMessage)
                ->setParameters(['{{length}}' => $constraint->minLength])
                ->addViolation()
            ;

            return;
        }

        $tips = [];

        if ($constraint->unicodeEquality) {
            $passwordStrength = $this->calculateStrengthUnicode($value, $tips);
        } else {
            $passwordStrength = $this->calculateStrength($value, $tips);
        }

        if ($passLength > 12) {
            ++$passwordStrength;
        } else {
            $tips[] = 'length';
        }

        // There is no decrease of strength on weak combinations.
        // Detecting this is tricky and requires a deep understanding of the syntax.

        if ($passwordStrength < $constraint->minStrength) {
            $parameters = [
                '{{ length }}' => $constraint->minLength,
                '{{ min_strength }}' => $this->translator->trans(/* @Ignore */ 'rollerworks_password.strength_level.' . self::$levelToLabel[$constraint->minStrength], [], 'validators'),
                '{{ current_strength }}' => $this->translator->trans(/* @Ignore */ 'rollerworks_password.strength_level.' . self::$levelToLabel[$passwordStrength], [], 'validators'),
                '{{ strength_tips }}' => implode(', ', array_map([$this, 'translateTips'], $tips)),
            ];

            $this->context->buildViolation($constraint->message)
                ->setParameters($parameters)
                ->addViolation()
            ;
        }
    }

    private function translateTips(string $tip): string
    {
        return $this->translator->trans(/* @Ignore */ 'rollerworks_password.tip.' . $tip, [], 'validators');
    }

    /**
     * @param array<int, string> $tips
     */
    private function calculateStrength(string $password, array &$tips): int
    {
        $passwordStrength = 0;

        if (preg_match('/[a-zA-Z]/', $password)) {
            ++$passwordStrength;

            if (! preg_match('/[a-z]/', $password)) {
                $tips[] = 'lowercase_letters';
            } elseif (preg_match('/[A-Z]/', $password)) {
                ++$passwordStrength;
            } else {
                $tips[] = 'uppercase_letters';
            }
        } else {
            $tips[] = 'letters';
        }

        if (preg_match('/\d+/', $password)) {
            ++$passwordStrength;
        } else {
            $tips[] = 'numbers';
        }

        if (preg_match('/[^a-zA-Z0-9]/', $password)) {
            ++$passwordStrength;
        } else {
            $tips[] = 'special_chars';
        }

        return $passwordStrength;
    }

    /**
     * @param array<int, string> $tips
     */
    private function calculateStrengthUnicode(string $password, array &$tips): int
    {
        $passwordStrength = 0;

        if (preg_match('/\p{L}/u', $password)) {
            ++$passwordStrength;

            if (! preg_match('/\p{Ll}/u', $password)) {
                $tips[] = 'lowercase_letters';
            } elseif (preg_match('/\p{Lu}/u', $password)) {
                ++$passwordStrength;
            } else {
                $tips[] = 'uppercase_letters';
            }
        } else {
            $tips[] = 'letters';
        }

        if (preg_match('/\p{N}/u', $password)) {
            ++$passwordStrength;
        } else {
            $tips[] = 'numbers';
        }

        if (preg_match('/[^\p{L}\p{N}]/u', $password)) {
            ++$passwordStrength;
        } else {
            $tips[] = 'special_chars';
        }

        return $passwordStrength;
    }
}
