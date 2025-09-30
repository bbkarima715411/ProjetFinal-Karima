<?php

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__.'/src', __DIR__.'/tests', __DIR__.'/config', __DIR__.'/public'])
    ->name('*.php')
    ->ignoreVCS(true)
    ->ignoreDotFiles(true)
    ->exclude(['var', 'vendor']);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setIndent("    ")
    ->setLineEnding("\n")
    ->setRules([
        '@Symfony' => true,
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => true,
        'no_unused_imports' => true,
        'binary_operator_spaces' => ['default' => 'align_single_space_minimal'],
        'yoda_style' => false,
        'single_quote' => true,
        'ternary_to_null_coalescing' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],
    ])
    ->setFinder($finder);
