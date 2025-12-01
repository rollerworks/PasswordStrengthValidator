<?php

declare(strict_types=1);

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

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class PasswordStrength extends Constraint
{
    public function __construct(
        public int $minStrength = 6,
        public ?int $minLength = null,
        public bool $unicodeEquality = false,
        public string $message = 'password_too_weak',
        public string $tooShortMessage = 'Your password must be at least {{length}} characters long.',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);
    }
}
