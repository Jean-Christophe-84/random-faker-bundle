<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Symfony\Set\SymfonySetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return RectorConfig::configure()
    ->withPaths(
        [
            __DIR__ . '/src',
        ]
    )
    ->withPreparedSets(deadCode: true, codeQuality: true)
    ->withPhpSets(php84: true)
    ->withSets(
        [
            SymfonySetList::SYMFONY_64,
            SymfonySetList::SYMFONY_CODE_QUALITY,
            SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
        ]
    )
    ->withRules(
        [
            AddVoidReturnTypeWhereNoReturnRector::class,
        ]
    );
