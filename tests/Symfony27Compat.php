<?php

/*
 * This file is part of the RollerworksPasswordStrengthValidator package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;

if (!class_exists(ConstraintValidatorTestCase::class)) {
    class_alias(AbstractConstraintValidatorTest::class, ConstraintValidatorTestCase::class);
}
