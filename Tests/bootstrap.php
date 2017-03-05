<?php
// Register composer autoloader
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    throw new \RuntimeException(
        'Could not find vendor/autoload.php, make sure you ran composer.'
    );
}

/** @var Composer\Autoload\ClassLoader $autoloader */
$autoloader = require __DIR__ . '/../vendor/autoload.php';

\FluidTYPO3\Development\Bootstrap::initialize(
    $autoloader,
    [
        'vhs_main' => \FluidTYPO3\Development\Bootstrap::CACHE_NULL,
        'vhs_markdown' => \FluidTYPO3\Development\Bootstrap::CACHE_NULL,
        'extbase_typo3dbbackend_tablecolumns' => \FluidTYPO3\Development\Bootstrap::CACHE_NULL,
        'extbase_typo3dbbackend_queries' => \FluidTYPO3\Development\Bootstrap::CACHE_NULL,
        'extbase_datamapfactory_datamap' => \FluidTYPO3\Development\Bootstrap::CACHE_NULL,
        'cache_rootline' => \FluidTYPO3\Development\Bootstrap::CACHE_NULL,
        'cache_pages' => \FluidTYPO3\Development\Bootstrap::CACHE_NULL,
        'cache_core' => \FluidTYPO3\Development\Bootstrap::CACHE_PHP_NULL,
        'extbase_object' => \FluidTYPO3\Development\Bootstrap::CACHE_NULL,
        'extbase_reflection' => \FluidTYPO3\Development\Bootstrap::CACHE_NULL,
        'l10n' => \FluidTYPO3\Development\Bootstrap::CACHE_NULL,
        'fluid_template' => \FluidTYPO3\Development\Bootstrap::CACHE_PHP_NULL
    ],
    [
        'core'
    ]
);
