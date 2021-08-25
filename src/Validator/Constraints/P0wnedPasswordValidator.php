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

use Rollerworks\Component\PasswordStrength\P0wnedPassword\Request\Client;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class P0wnedPasswordValidator extends ConstraintValidator
{
    /** @var Client */
    private $client;

    /**
     * P0wnedPasswordValidator constructor.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function validate($password, Constraint $constraint)
    {
        if ($password === null) {
            return;
        }

        if (! is_scalar($password) && ! (\is_object($password) && method_exists($password, '__toString'))) {
            throw new UnexpectedTypeException($password, 'string');
        }

        $password = (string) $password;

        $result = $this->client->check($password);

        if ($result->wasFound()) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ used }}', number_format($result->getUseCount()))
                ->addViolation()
            ;
        }
    }
}
