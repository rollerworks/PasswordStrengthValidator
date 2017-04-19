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

class PasswordRequirementsValidator extends ConstraintValidator
{
    /**
     * @param string                          $value
     * @param PasswordRequirements|Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if ($constraint->minLength > 0 && (mb_strlen($value) < $constraint->minLength)) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->tooShortMessage)
                    ->setParameters(array('{{length}}' => $constraint->minLength))
                    ->setInvalidValue($value)
                    ->addViolation();
            } else {
                $this->context->addViolation($constraint->tooShortMessage, array('{{length}}' => $constraint->minLength), $value);
            }
        }

        if ($constraint->requireLetters && !preg_match('/\pL/u', $value)) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->missingLettersMessage)
                    ->setInvalidValue($value)
                    ->addViolation();
            } else {
                $this->context->addViolation($constraint->missingLettersMessage, array(), $value);
            }
        }

        if ($constraint->requireCaseDiff && !preg_match('/(\p{Ll}+.*\p{Lu})|(\p{Lu}+.*\p{Ll})/u', $value)) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->requireCaseDiffMessage)
                    ->setInvalidValue($value)
                    ->addViolation();
            } else {
                $this->context->addViolation($constraint->requireCaseDiffMessage, array(), $value);
            }
        }

        if ($constraint->requireNumbers && !preg_match('/\pN/u', $value)) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->missingNumbersMessage)
                    ->setInvalidValue($value)
                    ->addViolation();
            } else {
                $this->context->addViolation($constraint->missingNumbersMessage, array(), $value);
            }
        }

        if ($constraint->requireSpecialCharacter && !preg_match('/[^p{Ll}\p{Lu}\pL\pN]/u', $value)) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->missingSpecialCharacterMessage)
                    ->setInvalidValue($value)
                    ->addViolation();
            } else {
                $this->context->addViolation($constraint->missingSpecialCharacterMessage, array(), $value);
            }
        }
    }
}
