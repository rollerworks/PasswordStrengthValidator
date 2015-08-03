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

use Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\BlacklistProviderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Password Blacklist Validation.
 *
 * Validates if the password is blacklisted/blocked for usage.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class BlacklistValidator extends ConstraintValidator
{
    /**
     * @var BlacklistProviderInterface
     */
    private $provider;

    /**
     * @param BlacklistProviderInterface $provider
     */
    public function __construct(BlacklistProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($password, Constraint $constraint)
    {
        if (null !== $password && !is_scalar($password) && !(is_object($password) && method_exists($password, '__toString'))) {
            throw new UnexpectedTypeException($password, 'string');
        }

        if (null === $password) {
            return;
        }

        if (true === $this->provider->isBlacklisted((string) $password)) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->message)
                    ->setInvalidValue($password)
                    ->addViolation();
            } else {
                $this->context->addViolation($constraint->message, array(), $password);
            }
        }
    }
}
