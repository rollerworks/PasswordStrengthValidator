<?php

$header = <<<EOF
This file is part of the RollerworksPasswordStrengthBundle package.

(c) Sebastiaan Stok <s.stok@rollerscapes.net>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

return PhpCsFixer\Config::create()
    ->setRules(array(
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_syntax' => array('syntax' => 'long'),
        'no_unreachable_default_argument_value' => false,
        'braces' => array('allow_single_line_closure' => true),
        'heredoc_to_nowdoc' => false,
        'phpdoc_annotation_without_dot' => false,
    ))
    ->setRiskyAllowed(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(array(__DIR__.'/src', __DIR__.'/tests'))
    )
;
