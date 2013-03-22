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
use Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\BlacklistProviderInterface;

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
     * {@inheritDoc}
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
            $this->context->addViolation($constraint->message, array(), $password);
        }
    }
}
