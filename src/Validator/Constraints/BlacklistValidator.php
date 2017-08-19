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

use Rollerworks\Component\PasswordStrength\Blacklist\BlacklistProviderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
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
        if (null === $password) {
            return;
        }

        if (!is_scalar($password) && !(is_object($password) && method_exists($password, '__toString'))) {
            throw new UnexpectedTypeException($password, 'string');
        }

        if (true === $this->provider->isBlacklisted((string) $password)) {
            $this->context->buildViolation($constraint->message)
                ->setInvalidValue($password)
                ->addViolation();
        }
    }
}
