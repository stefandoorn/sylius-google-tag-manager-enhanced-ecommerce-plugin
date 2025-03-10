<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $config): void {
    $config->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests/Behat',
        __DIR__ . '/tests/Unit',
        __DIR__ . '/tests/Functional',
        __DIR__ . '/ecs.php',
    ]);

    $config->import('vendor/sylius-labs/coding-standard/ecs.php');

    $config->ruleWithConfiguration(BinaryOperatorSpacesFixer::class, []);
};
