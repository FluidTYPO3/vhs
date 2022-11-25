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
    /**
     * @param null|string $message
     * @param null|integer $code
     * @return void
     */
    public static function throwViewHelperException($message = null, $code = null)
    {
        throw new Exception((string) $message, (integer) $code);
    }
}
