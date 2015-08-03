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

        $passwordStrength = 0;
        $passLength = strlen($password);

        if ($passLength < $constraint->minLength) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->message)
                    ->setParameters(array('{{ length }}' => $constraint->minLength))
                    ->addViolation();
            } else {
                $this->context->addViolation($constraint->message, array('{{ length }}' => $constraint->minLength));
            }

            return;
        }

        if (preg_match('/[a-zA-Z]/', $password)) {
            ++$passwordStrength;
            if (preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password)) {
                ++$passwordStrength;
            }
        }

        if (preg_match('/\d+/', $password)) {
            ++$passwordStrength;
        }

        if (preg_match('/[^a-zA-Z0-9]/', $password)) {
            ++$passwordStrength;
        }

        if ($passLength > 12) {
            ++$passwordStrength;
        }

        // No decrease strength on weak combinations

        if ($passwordStrength < $constraint->minStrength) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->message)
                    ->setParameters(array('{{ length }}' => $constraint->minLength))
                    ->addViolation();
            } else {
                $this->context->addViolation($constraint->message, array('{{ length }}' => $constraint->minLength));
            }
        }
    }
}
