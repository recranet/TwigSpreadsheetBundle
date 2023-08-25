<?php

if (!file_exists(__DIR__.'/src')) {
    exit(0);
}

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_syntax' => ['syntax' => 'short'],
        'combine_consecutive_unsets' => true,
        // one should use PHPUnit methods to set up expected exception instead of annotations
        'general_phpdoc_annotation_remove' => [
            'annotations' => ['expectedException', 'expectedExceptionMessage', 'expectedExceptionMessageRegExp'],
        ],
        'heredoc_to_nowdoc' => true,
        'echo_tag_syntax' => true,
        'no_extra_blank_lines' => [
            'tokens' => ['break', 'continue', 'extra', 'return', 'throw', 'use', 'parenthesis_brace_block', 'square_brace_block', 'curly_brace_block'],
        ],
        'no_superfluous_phpdoc_tags' => false,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'operator_linebreak' => [
            'only_booleans' => true,
            'position' => 'end',
        ],
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'phpdoc_add_missing_param_annotation' => [
            'only_untyped' => false,
        ],
        'phpdoc_order' => true,
        'phpdoc_var_without_name' => false,
        'semicolon_after_instruction' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'yoda_style' => false,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__.'/src')
            ->in(__DIR__.'/tests')
            ->append([__FILE__])
    )
;
