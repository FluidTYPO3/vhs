<?php

namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class AccessibleExtensionManagementUtility extends ExtensionManagementUtility
{
    public static function setPackageManager(PackageManager $packageManager): void
    {
        static::$packageManager = $packageManager;
    }
}
