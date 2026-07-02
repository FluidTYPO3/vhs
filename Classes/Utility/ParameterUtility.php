<?php

namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

class ParameterUtility
{
    public static function resolveParameterValue(string $name): mixed
    {
        if (VersionUtility::isCoreBelow14()) {
            return GeneralUtility::getIndpEnv($name);
        }
        $params = RequestResolver::getNormalizedParameters();
        return match ($name) {
            'TYPO3_SITE_URL' => $params?->getSiteUrl(),
            default => throw new \UnexpectedValueException('Unsupported parameter: ' . $name, 1781860550),
        };
    }
}
