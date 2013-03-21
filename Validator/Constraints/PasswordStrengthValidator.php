<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Password strength Validation.
 *
 * Validates if the password strength is equal or higher
 * to the required minimum.
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
 *
 * @todo Check for long passwords consisting of only repeated characters like 1234567910
 * @todo Add support for checking the password against a weak/forbidden password database
 */
class PasswordStrengthValidator extends ConstraintValidator
{
    /**
     * {@inheritDoc}
     */
    public function validate($password, Constraint $constraint)
    {
        if (null !== $password && !is_scalar($password) && !(is_object($password) && method_exists($password, '__toString'))) {
            throw new UnexpectedTypeException($password, 'string');
        }

        $password = (string) $password;

        $passwordStrength = 0;
        $passLength       = strlen($password);

        if ($passLength < $constraint->minLength) {
            $this->context->addViolation($constraint->message, array('{{ length }}' => $constraint->minLength));

            return;
        }

        $alpha = $digit = $specialChar = false;

        if ($passLength >= 8) {
            $passwordStrength = 1;
        }

        if (preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password)) {
            $alpha = true;
            $passwordStrength++;
        }

        if (preg_match('/\d+/', $password)) {
            $digit = true;
            $passwordStrength++;
        }

        if (preg_match('/.[^a-zA-Z0-9]/', $password)) {
            $specialChar = true;
            $passwordStrength++;
        }

        if ($passLength > 12) {
            $passwordStrength++;
        }

        // No decrease strength on weak combinations

        // Only digits no alpha or special char
        if ($digit && !$alpha && !$specialChar) {
            $passwordStrength--;
        } elseif ($alpha && !$digit) {
            $passwordStrength--;
        }

        if ($passwordStrength < $constraint->minStrength) {
            $this->context->addViolation($constraint->message, array('{{ length }}' => $constraint->minLength));
        }
    }
}
