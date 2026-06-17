<?php
// Register composer autoloader
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    throw new \RuntimeException(
        'Could not find vendor/autoload.php, make sure you ran composer.'
    );
}

/** @var Composer\Autoload\ClassLoader $autoloader */
$autoloader = require __DIR__ . '/../vendor/autoload.php';

$forceErrorMode = getenv('FORCE_ERROR_MODE');
if ((string)$forceErrorMode === '') {
    $errorMode = E_ERROR | E_WARNING | E_PARSE;
} else {
    $errorMode = (int)$forceErrorMode;
}

ini_set('error_reporting', $errorMode);
