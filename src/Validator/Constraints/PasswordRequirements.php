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
class PasswordRequirements extends Constraint
{
    public $tooShortMessage = 'Your password must be at least {{length}} characters long.';
    public $missingLettersMessage = 'Your password must include at least one letter.';
    public $requireCaseDiffMessage = 'Your password must include both upper and lower case letters.';
    public $missingNumbersMessage = 'Your password must include at least one number.';
    public $missingSpecialCharacterMessage = 'Your password must contain at least one special character.';

    public $minLength = 6;
    public $requireLetters = true;
    public $requireCaseDiff = false;
    public $requireNumbers = false;
    public $requireSpecialCharacter = false;

    /**
     * PasswordRequirements constructor.
     *
     * @param array|null  $options
     * @param string|null $tooShortMessage
     * @param string|null $missingLettersMessage
     * @param string|null $requireCaseDiffMessage
     * @param string|null $missingNumbersMessage
     * @param string|null $missingSpecialCharacterMessage
     * @param int|null    $minLength
     * @param bool        $requireLetters
     * @param bool        $requireCaseDiff
     * @param bool        $requireNumbers
     * @param bool        $requireSpecialCharacter
     * @param array|null  $groups
     * @param null        $payload
     */
    public function __construct(
        array $options = null,
        string $tooShortMessage = null,
        string $missingLettersMessage = null,
        string $requireCaseDiffMessage = null,
        string $missingNumbersMessage = null,
        string $missingSpecialCharacterMessage = null,
        int $minLength = null,
        bool $requireLetters = null,
        bool $requireCaseDiff = null,
        bool $requireNumbers = null,
        bool $requireSpecialCharacter = null,
        array $groups = null,
        $payload = null
    ) {
        if ($tooShortMessage) {
            $options['tooShortMessage'] = $tooShortMessage;
        }
        if ($missingLettersMessage) {
            $options['missingLettersMessage'] = $missingLettersMessage;
        }
        if ($requireCaseDiffMessage) {
            $options['requireCaseDiffMessage'] = $requireCaseDiffMessage;
        }
        if ($missingNumbersMessage) {
            $options['missingNumbersMessage'] = $missingNumbersMessage;
        }
        if ($missingSpecialCharacterMessage) {
            $options['missingSpecialCharacterMessage'] = $missingSpecialCharacterMessage;
        }
        if ($minLength) {
            $options['minLength'] = $minLength;
        }
        if ($requireLetters) {
            $options['requireLetters'] = $requireLetters;
        }
        if ($requireCaseDiff) {
            $options['requireCaseDiff'] = $requireCaseDiff;
        }
        if ($requireNumbers) {
            $options['requireNumbers'] = $requireNumbers;
        }
        if ($requireSpecialCharacter) {
            $options['requireSpecialCharacter'] = $requireSpecialCharacter;
        }

        parent::__construct($options ?? [], $groups, $payload);
    }
}
