<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * Error Utility
 *
 * Utility to assist with error throwing on different TYPO3 version
 */
class ErrorUtility
{
    public static function throwViewHelperException(?string $message = null, ?int $code = null): void
    {
        throw new Exception((string) $message, (integer) $code);
    }
}
