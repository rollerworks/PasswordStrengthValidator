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

use Psr\Container\ContainerInterface;
use Rollerworks\Component\PasswordStrength\Blacklist\BlacklistProviderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\RuntimeException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Password Blacklist Validation.
 *
 * Validates if the password is blacklisted/blocked for usage.
 */
class BlacklistValidator extends ConstraintValidator
{
    private $defaultProvider;
    private $providersLoader;

    /**
     * @param ContainerInterface $providersLoader Service-container for loading
     *                                            blacklist providers
     */
    public function __construct(BlacklistProviderInterface $defaultProvider, ContainerInterface $providersLoader = null)
    {
        $this->defaultProvider = $defaultProvider;
        $this->providersLoader = $providersLoader;
    }

    /**
     * @param string|null          $password
     * @param Constraint|Blacklist $constraint
     */
    public function validate($password, Constraint $constraint)
    {
        if ($password === null) {
            return;
        }

        if (! is_scalar($password) && ! (\is_object($password) && method_exists($password, '__toString'))) {
            throw new UnexpectedTypeException($password, 'string');
        }

        if ($constraint->provider === null) {
            $provider = $this->defaultProvider;
        } else {
            if ($this->providersLoader === null || ! $this->providersLoader->has($constraint->provider)) {
                throw new RuntimeException(sprintf('Unable to use blacklist provider "%s", eg. no blacklists were configured or this provider is not supported.', $constraint->provider));
            }

            $provider = $this->providersLoader->get($constraint->provider);
        }

        if ($provider->isBlacklisted((string) $password) === true) {
            $this->context->buildViolation($constraint->message)
                ->setInvalidValue($password)
                ->addViolation()
            ;
        }
    }
}
