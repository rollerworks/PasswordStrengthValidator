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

    public function __construct(
        $options = null,
        ?array $groups = null,
               $payload = null,
        ?int $minLength = null,
        ?bool $requireLetters = null,
        ?bool $requireCaseDiff = null,
        ?bool $requireNumbers = null,
        ?bool $requireSpecialCharacter = null,
        ?string $tooShortMessage = null,
        ?string $missingLettersMessage = null,
        ?string $requireCaseDiffMessage = null,
        ?string $missingNumbersMessage = null,
        ?string $missingSpecialCharacterMessage = null
    ) {
        parent::__construct($options ?? [], $groups, $payload);

        $this->tooShortMessage = $tooShortMessage ?? $this->tooShortMessage;
        $this->missingLettersMessage = $missingLettersMessage ?? $this->missingLettersMessage;
        $this->requireCaseDiffMessage = $requireCaseDiffMessage ?? $this->requireCaseDiffMessage;
        $this->missingNumbersMessage = $missingNumbersMessage ?? $this->missingNumbersMessage;
        $this->missingSpecialCharacterMessage = $missingSpecialCharacterMessage ?? $this->missingSpecialCharacterMessage;

        $this->minLength = $minLength ?? $this->minLength;
        $this->requireLetters = $requireLetters ?? $this->requireLetters;
        $this->requireCaseDiff = $requireCaseDiff ?? $this->requireCaseDiff;
        $this->requireNumbers = $requireNumbers ?? $this->requireNumbers;
        $this->requireSpecialCharacter = $requireSpecialCharacter ?? $this->requireSpecialCharacter;
    }
}
