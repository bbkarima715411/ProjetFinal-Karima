<?php

use TwigCsFixer\Config\Config;
use TwigCsFixer\Ruleset\Official;
use TwigCsFixer\Finder;

$finder = Finder::create()
    ->in([__DIR__.'/templates'])
    ->name('*.twig');

$config = new Config();
$config->setFinder($finder);
$config->setRuleset(new Official());

return $config;
