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

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class PasswordStrength extends Constraint
{
    public $tooShortMessage = 'Your password must be at least {{length}} characters long.';
    public $message = 'password_too_weak';
    public $minLength = 6;
    public $minStrength;
    public $unicodeEquality = false;

    public function __construct(
        $options = null,
        array $groups = null,
              $payload = null,
        ?int $minStrength = null,
        int $minLength = null,
        bool $unicodeEquality = null,
        string $message = null,
        string $tooShortMessage = null
    ) {
        parent::__construct($options ?? [], $groups, $payload);

        $this->minStrength = $minStrength ?? $this->minStrength;
        $this->minLength = $minLength ?? $this->minLength;
        $this->unicodeEquality = $unicodeEquality ?? $this->unicodeEquality;
        $this->message = $message ?? $this->message;
        $this->tooShortMessage = $tooShortMessage ?? $this->tooShortMessage;
    }

    public function getDefaultOption(): string
    {
        return 'minStrength';
    }

    public function getRequiredOptions(): array
    {
        return ['minStrength'];
    }
}
