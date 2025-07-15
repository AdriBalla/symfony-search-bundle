<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@PSR12' => true,
        '@PhpCsFixer' => true,
        '@PHP84Migration' => true,
        '@PHPUnit100Migration:risky' => false,
        'linebreak_after_opening_tag' => true,
        'phpdoc_separation' => false,
        'phpdoc_align' => [
            'tags' => [
                'param',
                'return',
                'throws',
                'type',
                'var',
            ],
        ],
        'php_unit_test_class_requires_covers' => false,
        'single_line_throw' => false,
        'octal_notation' => false,
        'trailing_comma_in_multiline' => [
            'elements' => ['arguments', 'arrays', 'match', 'parameters'],
        ],
        'no_superfluous_phpdoc_tags' => false,
        'phpdoc_no_useless_inheritdoc' => false,
        'phpdoc_to_param_type' => false,
        'phpdoc_to_property_type' => false,
        'phpdoc_to_return_type' => false,
        'fully_qualified_strict_types' => [
            'leading_backslash_in_global_namespace' => true,
        ],
        'php_unit_internal_class' => false,

    ])
    ->setFinder($finder)
;
