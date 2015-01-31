<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\PasswordStrengthBundle\Tests\Validator;

use Symfony\Component\Validator\Validation;

class LegacyPasswordStrength2Dot4ApiTest extends PasswordStrengthTest
{
    protected function getApiVersion()
    {
        return Validation::API_VERSION_2_4;
    }
}
