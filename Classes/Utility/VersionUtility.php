<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\VersionNumberUtility;

class VersionUtility
{
    public static function isCoreAtLeast13(): bool
    {
        return version_compare(self::getCoreVersion(), '13.4', '>=');
    }

    public static function isCoreAtLeast14(): bool
    {
        return version_compare(self::getCoreVersion(), '14.3', '>=');
    }

    public static function isCoreBelow14(): bool
    {
        return version_compare(self::getCoreVersion(), '14', '<');
    }

    private static function getCoreVersion(): string
    {
        return VersionNumberUtility::getCurrentTypo3Version();
    }
}
