<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

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
     */
    public static function throwViewHelperException($message = null, $code = null)
    {
        if (version_compare(TYPO3_version, '8.0', '>=')) {
            throw new \TYPO3Fluid\Fluid\Core\ViewHelper\Exception($message, $code);
        }
        throw new \TYPO3\CMS\Fluid\Core\ViewHelper\Exception($message, $code);
    }
}
