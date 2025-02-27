<?php

$version = $argv[1];

if (empty($version)) {
    echo 'Version must be specified as first argument';
    exit(1);
}

$command = function (string $command): void {
    echo $command . PHP_EOL;
    system($command);
};

$composerArguments = [
    '10.4' => '--ignore-platform-reqs',
];

$currentLockFile = null;
if (file_exists('composer.lock')) {
    $currentLockFile = 'composer.lock';
}

$currentVendorDir = null;
if (file_exists('vendor')) {
    $currentVendorDir = 'vendor';
}

$preflightDirectory = 'preflight/';
$directory = $preflightDirectory . $version . '/';

// Check if a preserved "vendor" and "composer.lock" exists for the version. If not, create them.
if (!file_exists($directory)) {
    echo 'Creating: ' . $directory . PHP_EOL;
    mkdir($directory);
}

$versionSpecificVendorDirectory = $directory . 'vendor';
if (!file_exists($versionSpecificVendorDirectory)) {
    if ($currentLockFile && file_exists($currentLockFile)) {
        $command('rm -rf ' . $currentLockFile);
    }
    if ($currentVendorDir && file_exists($currentVendorDir)) {
        $command('rm -rf ' . $currentVendorDir);
    }
    $command(
        'composer req typo3/cms-core:^'
        . $version
        . (isset($composerArguments[$version]) ? ' ' . $composerArguments[$version] : '')
    );
    $command('cp composer.lock ' . $directory . 'composer.lock');
    $command('git checkout composer.json');
    $command('mv vendor ' . $directory);
}

if ($currentLockFile && file_exists($currentLockFile)) {
    $command('rm -rf ' . $currentLockFile);
}

if ($currentVendorDir && file_exists($currentVendorDir)) {
    $command('rm -rf ' . $currentVendorDir);
}

$command('cp -R ' . $directory . 'composer.lock ./composer.lock');
$command('cp -R ' . $directory . 'vendor ./vendor');
$command('composer install' . (isset($composerArguments[$version]) ? ' ' . $composerArguments[$version] : ''));
