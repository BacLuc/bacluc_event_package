<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR2' => true,
        '@DoctrineAnnotation' => true,
        '@PhpCsFixer' => true,
        'curly_braces_position' => [
            'functions_opening_brace' => 'same_line',
            'classes_opening_brace' => 'same_line'
        ],
        'php_unit_test_class_requires_covers' => false
    ])
    ->setFinder($finder)
    ;