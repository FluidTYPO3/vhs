<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

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
        return ExtensionManagementUtility::extPath('core') . 'Resources/Public/Icons/Flags/SVG/';
    }

    /**
     * Returns the current core minor version
     *
     * @return string
     * @throws \TYPO3\CMS\Core\Package\Exception
     */
    public static function getCurrentCoreVersion()
    {
        return substr(ExtensionManagementUtility::getExtensionVersion('core'), 0, 3);
    }
}
