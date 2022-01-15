<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR2' => true,
        '@DoctrineAnnotation' => true,
        '@PhpCsFixer' => true,
        'braces' => ['position_after_functions_and_oop_constructs' => 'same'],
        'php_unit_test_class_requires_covers' => false
    ])
    ->setFinder($finder)
    ;