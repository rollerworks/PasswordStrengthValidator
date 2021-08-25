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

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PasswordRequirementsValidator extends ConstraintValidator
{
    /**
     * @param string|null                     $value
     * @param PasswordRequirements|Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value === null || $value === '') {
            return;
        }

        if (! is_scalar($value) && ! (\is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if ($constraint->minLength > 0 && (mb_strlen($value) < $constraint->minLength)) {
            $this->context->buildViolation($constraint->tooShortMessage)
                ->setParameters(['{{length}}' => $constraint->minLength])
                ->setInvalidValue($value)
                ->addViolation()
            ;
        }

        if ($constraint->requireLetters && ! preg_match('/\pL/u', $value)) {
            $this->context->buildViolation($constraint->missingLettersMessage)
                ->setInvalidValue($value)
                ->addViolation()
            ;
        }

        if ($constraint->requireCaseDiff && ! preg_match('/(\p{Ll}+.*\p{Lu})|(\p{Lu}+.*\p{Ll})/u', $value)) {
            $this->context->buildViolation($constraint->requireCaseDiffMessage)
                ->setInvalidValue($value)
                ->addViolation()
            ;
        }

        if ($constraint->requireNumbers && ! preg_match('/\pN/u', $value)) {
            $this->context->buildViolation($constraint->missingNumbersMessage)
                ->setInvalidValue($value)
                ->addViolation()
            ;
        }

        if ($constraint->requireSpecialCharacter && ! preg_match('/[^\p{Ll}\p{Lu}\pL\pN]/u', $value)) {
            $this->context->buildViolation($constraint->missingSpecialCharacterMessage)
                ->setInvalidValue($value)
                ->addViolation()
            ;
        }
    }
}
