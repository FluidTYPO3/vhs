<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * Core Utility
 *
 * Utility to get core informations.
 */
class CoreUtility
{
    /**
     * Returns the flag icons path depending on the current core version
     *
     * @return string
     */
    public static function getLanguageFlagIconPath()
    {
        $coreExtensionPath = ExtensionManagementUtility::extPath('core');
        if (version_compare(static::getCurrentCoreVersion(), '9.0', '<')) {
            return $coreExtensionPath . 'Resources/Public/Icons/Flags/PNG/';
        }
        return $coreExtensionPath . 'Resources/Public/Icons/Flags/';
    }

    public static function getSitePath(): string
    {
        if (defined('PATH_site')) {
            return PATH_site;
        }
        /** @see https://docs.typo3.org/m/typo3/reference-coreapi/9.5/en-us/ApiOverview/GlobalValues/Constants/Index.html#path-site */
        return Environment::getPublicPath() . '/';
    }

    /**
     * Returns the current core minor version
     *
     * @return string
     * @throws \TYPO3\CMS\Core\Package\Exception
     */
    public static function getCurrentCoreVersion()
    {
        return VersionNumberUtility::getCurrentTypo3Version();
    }
}
