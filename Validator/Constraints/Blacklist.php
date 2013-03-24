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
