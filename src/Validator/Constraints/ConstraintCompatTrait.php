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
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;

if (method_exists(Constraint::class, 'normalizeOptions')) {
    /**
     * @internal
     */
    trait ConstraintCompatTrait
    {
        protected function initOptions(?array $options, ?array $groups, mixed $payload): void
        {
            // Noop
        }
    }
} else {

    /**
     * @internal
     */
    trait ConstraintCompatTrait
    {
        protected function initOptions(?array $options, ?array $groups, mixed $payload): void
        {
            if ($options === null) {
                return;
            }

            trigger_deprecation('symfony/validator', '7.4', 'Support for evaluating options in the %1$s class is deprecated. Initialize properties in the constructor of %1$s instead.', static::class);

            $options = $this->normalizeOptions($options);

            if ($groups !== null) {
                $options['groups'] = $groups;
            }
            $options['payload'] = $payload ?? $options['payload'] ?? null;

            foreach ($options as $name => $value) {
                $this->{$name} = $value;
            }
        }

        /**
         * @deprecated since Symfony 7.4
         *
         * @return array<string, mixed>
         */
        protected function normalizeOptions(mixed $options): array
        {
            $normalizedOptions = [];
            $defaultOption = $this->getDefaultOption(false);
            $invalidOptions = [];
            $missingOptions = array_flip($this->getRequiredOptions(false));
            $knownOptions = get_class_vars(static::class);

            if (\is_array($options) && isset($options['value']) && ! property_exists($this, 'value')) {
                if ($defaultOption === null) {
                    throw new ConstraintDefinitionException(\sprintf('No default option is configured for constraint "%s".', static::class));
                }

                $options[$defaultOption] = $options['value'];
                unset($options['value']);
            }

            if (\is_array($options)) {
                reset($options);
            }

            if ($options && \is_array($options) && \is_string(key($options))) {
                foreach ($options as $option => $value) {
                    if (\array_key_exists($option, $knownOptions)) {
                        $normalizedOptions[$option] = $value;
                        unset($missingOptions[$option]);
                    } else {
                        $invalidOptions[] = $option;
                    }
                }
            } elseif ($options !== null && ! (\is_array($options) && \count($options) === 0)) {
                if ($defaultOption === null) {
                    throw new ConstraintDefinitionException(\sprintf('No default option is configured for constraint "%s".', static::class));
                }

                if (\array_key_exists($defaultOption, $knownOptions)) {
                    $normalizedOptions[$defaultOption] = $options;
                    unset($missingOptions[$defaultOption]);
                } else {
                    $invalidOptions[] = $defaultOption;
                }
            }

            if (\count($invalidOptions) > 0) {
                throw new InvalidOptionsException(\sprintf('The options "%s" do not exist in constraint "%s".', implode('", "', $invalidOptions), static::class), $invalidOptions);
            }

            if (\count($missingOptions) > 0) {
                throw new MissingOptionsException(\sprintf('The options "%s" must be set for constraint "%s".', implode('", "', array_keys($missingOptions)), static::class), array_keys($missingOptions));
            }

            return $normalizedOptions;
        }

        /**
         * Returns the name of the default option.
         *
         * Override this method to define a default option.
         *
         * @deprecated since Symfony 7.4
         * @see __construct()
         */
        public function getDefaultOption(): ?string
        {
            if (\func_num_args() === 0 || func_get_arg(0)) {
                trigger_deprecation('symfony/validator', '7.4', 'The %s() method is deprecated.', __METHOD__);
            }

            return null;
        }

        /**
         * Returns the name of the required options.
         *
         * Override this method if you want to define required options.
         *
         * @return string[]
         *
         * @deprecated since Symfony 7.4
         * @see __construct()
         */
        public function getRequiredOptions(): array
        {
            if (\func_num_args() === 0 || func_get_arg(0)) {
                trigger_deprecation('symfony/validator', '7.4', 'The %s() method is deprecated.', __METHOD__);
            }

            return [];
        }
    }
}
