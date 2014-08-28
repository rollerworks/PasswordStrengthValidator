<?php

/**
 * This file is part of the RollerworksPasswordStrengthBundle package.
 *
 * (c) 2012-2014 Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Blacklist extends Constraint
{
    public $message = 'password_blacklisted';

    /**
     * {@inheritDoc}
     */
    public function validatedBy()
    {
        return 'rollerworks_password_strength.blacklist.validator';
    }
}
