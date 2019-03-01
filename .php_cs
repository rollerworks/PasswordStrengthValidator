<?php

$header = <<<EOF
This file is part of the RollerworksPasswordStrengthValidator package.

(c) Sebastiaan Stok <s.stok@rollerscapes.net>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

return PhpCsFixer\Config::create()
    ->setRules(array(
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unreachable_default_argument_value' => false,
        'braces' => ['allow_single_line_closure' => true],
        'header_comment' => ['header' => $header],
        'heredoc_to_nowdoc' => false,
        'phpdoc_annotation_without_dot' => false,
        'yoda_style' => false,
        'native_function_invocation' => false
    ))
    ->setRiskyAllowed(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in([__DIR__.'/src', __DIR__.'/tests'])
    )
;
