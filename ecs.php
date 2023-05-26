<?php

use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $config): void {
    $config->import('vendor/sylius-labs/coding-standard/ecs.php');

    $config->skip([
        VisibilityRequiredFixer::class => ['*Spec.php'],
        'tests/Application/*',
    ]);

    $config->ruleWithConfiguration(BinaryOperatorSpacesFixer::class, []);

    $services = $config->services();
    $services->set(
        TrailingCommaInMultilineFixer::class
    )->call('configure', [['elements' => ['arrays']]]);
};
