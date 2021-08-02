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

    /**
     * PasswordStrength constructor.
     *
     * @param array|null  $options
     * @param string|null $tooShortMessage
     * @param string|null $message
     * @param int|null    $minLength
     * @param null        $minStrength
     * @param bool        $unicodeEquality
     * @param array|null  $groups
     * @param null        $payload
     */
    public function __construct(
        array $options = null,
        string $tooShortMessage = null,
        string $message = null,
        int $minLength = null,
        $minStrength = null,
        bool $unicodeEquality = null,
        array $groups = null,
        $payload = null
    ) {
        if ($tooShortMessage) {
            $options['tooShortMessage'] = $tooShortMessage;
        }
        if ($message) {
            $options['message'] = $message;
        }
        if ($minLength) {
            $options['minLength'] = $minLength;
        }
        if ($minStrength) {
            $options['minStrength'] = $minStrength;
        }
        if ($unicodeEquality) {
            $options['unicodeEquality'] = $unicodeEquality;
        }

        parent::__construct($options ?? [], $groups, $payload);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'minStrength';
    }

    public function getRequiredOptions()
    {
        return ['minStrength'];
    }
}
