<?php

$versions = ['13.4', '12.4', '11.5', '10.4'];

$currentLockFile = null;
if (file_exists('composer.lock')) {
    $currentLockFile = file_get_contents('composer.lock');
}

$currentVendorDir = null;
if (file_exists('vendor')) {
    $currentVendorDir = 'vendor';
}

$preflightDirectory = 'preflight/';

system('php -d "xdebug.mode=off" ./vendor/bin/phpcs --standard="PSR2" Classes Tests');

foreach ($versions as $version) {
    echo PHP_EOL;
    echo PHP_EOL;
    echo '--------------- VERSION: ' . $version . ' --------------------' . PHP_EOL;
    echo PHP_EOL;
    // Check if a preserved "vendor" and "composer.lock" exists for the version. If not, create them.
    system('php -d "xdebug.mode=off" Tests/switch_version.php ' . $version);
    system('php -d "xdebug.mode=off" ./vendor/bin/phpstan');
    system('php -d "xdebug.mode=off" ./vendor/bin/phpunit');
}

system('php -d "xdebug.mode=off" Tests/switch_version.php ' . reset($versions));
