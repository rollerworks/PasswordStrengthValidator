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
class PasswordRequirements extends Constraint
{
    public function __construct(
        public string $tooShortMessage = 'Your password must be at least {{length}} characters long.',
        public string $missingLettersMessage = 'Your password must include at least one letter.',
        public string $requireCaseDiffMessage = 'Your password must include both upper and lower case letters.',
        public string $missingNumbersMessage = 'Your password must include at least one number.',
        public string $missingSpecialCharacterMessage = 'Your password must contain at least one special character.',
        public int $minLength = 6,
        public bool $requireLetters = true,
        public bool $requireCaseDiff = false,
        public bool $requireNumbers = false,
        public bool $requireSpecialCharacter = false,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct($groups, $payload);
    }
}
