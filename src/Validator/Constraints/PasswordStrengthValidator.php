<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints;

use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

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
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Shouvik Chatterjee <mailme@shouvik.net>
 */
class PasswordStrengthValidator extends ConstraintValidator
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private static $levelToLabel = array(
        1 => 'very_weak',
        2 => 'weak',
        3 => 'medium',
        4 => 'strong',
        5 => 'very_strong',
    );

    public function __construct(TranslatorInterface $translator = null)
    {
        // If translator is missing create a new translator.
        // With the 'en' locale and 'validators' domain.
        if (null === $translator) {
            $translator = new Translator('en');
            $translator->addLoader('xlf', new XliffFileLoader());
            $translator->addResource('xlf', dirname(dirname(__DIR__)).'/Resources/translations/validators.en.xlf', 'en', 'validators');
        }

        $this->translator = $translator;
    }

    /**
     * @param string                      $password
     * @param PasswordStrength|Constraint $constraint
     */
    public function validate($password, Constraint $constraint)
    {
        if (null === $password || '' === $password) {
            return;
        }

        if (null !== $password && !is_scalar($password) && !(is_object($password) && method_exists($password, '__toString'))) {
            throw new UnexpectedTypeException($password, 'string');
        }

        $password = (string) $password;
        $passLength = mb_strlen($password);

        if ($passLength < $constraint->minLength) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->tooShortMessage)
                    ->setParameters(array('{{length}}' => $constraint->minLength))
                    ->addViolation();
            } else {
                $this->context->addViolation($constraint->tooShortMessage, array('{{length}}' => $constraint->minLength));
            }

            return;
        }

        $tips = array();

        if ($constraint->unicodeEquality) {
            $passwordStrength = $this->calculateStrengthUnicode($password, $tips);
        } else {
            $passwordStrength = $this->calculateStrength($password, $tips);
        }

        if ($passLength > 12) {
            ++$passwordStrength;
        } else {
            $tips[] = 'length';
        }

        // No decrease strength on weak combinations

        if ($passwordStrength < $constraint->minStrength) {
            $parameters = array(
                '{{ length }}' => $constraint->minLength,
                '{{ min_strength }}' => $this->translator->trans('rollerworks_password.strength_level.'.self::$levelToLabel[$constraint->minStrength], array(), 'validators'),
                '{{ current_strength }}' => $this->translator->trans('rollerworks_password.strength_level.'.self::$levelToLabel[$passwordStrength], array(), 'validators'),
                '{{ strength_tips }}' => implode(', ', array_map(array($this, 'translateTips'), $tips)),
            );

            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->message)
                    ->setParameters($parameters)
                    ->addViolation();
            } else {
                $this->context->addViolation($constraint->message, $parameters);
            }
        }
    }

    /**
     * @internal
     */
    public function translateTips($tip)
    {
        return $this->translator->trans('rollerworks_password.tip.'.$tip, array(), 'validators');
    }

    private function calculateStrength($password, &$tips)
    {
        $passwordStrength = 0;

        if (preg_match('/[a-zA-Z]/', $password)) {
            ++$passwordStrength;

            if (!preg_match('/[a-z]/', $password)) {
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

    private function calculateStrengthUnicode($password, &$tips)
    {
        $passwordStrength = 0;

        if (preg_match('/\p{L}/u', $password)) {
            ++$passwordStrength;

            if (!preg_match('/\p{Ll}/u', $password)) {
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
