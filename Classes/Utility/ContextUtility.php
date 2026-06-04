<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;

class ContextUtility
{
    public static function isFrontend(?ServerRequestInterface $request = null): bool
    {
        $request ??= $GLOBALS['TYPO3_REQUEST'] ?? null;
        if (!$request instanceof ServerRequestInterface) {
            return false;
        }
        return ApplicationType::fromRequest($request)->isFrontend();
    }

    public static function isBackend(?ServerRequestInterface $request = null): bool
    {
        $request ??= $GLOBALS['TYPO3_REQUEST'] ?? null;
        if (!$request instanceof ServerRequestInterface) {
            return false;
        }
        return ApplicationType::fromRequest($request)->isBackend();
    }
}
