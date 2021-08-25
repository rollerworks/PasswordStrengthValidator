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

trigger_deprecation('rollerworks/password-strength-validator', '1.4', 'The P0wnedPassword validator is deprecated and will be removed in the next major version. Use the NotCompromisedPassword validator from the symfony/validator package instead.', P0wnedPassword::class);

/**
 * @Annotation
 *
 * @deprecated since rollerworks/password-strength-validator 1.4 The P0wnedPassword validator is deprecated and will be removed in the next major version. Use the NotCompromisedPassword validator from the symfony/validator package instead.
 */
class P0wnedPassword extends Constraint
{
    public $message = 'This password was found in a database of compromised passwords. It has been used {{ used }} times. For security purposes you must use something else.';

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
