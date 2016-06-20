<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Charset\CharsetConverter;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Frontend Simulation Utility
 *
 * Utility to simulate frontend enviroment in backend enviroment.
 */
class FrontendSimulationUtility
{

    /**
     * Sets the global variables $GLOBALS['TSFE']->csConvObj and $GLOBALS['TSFE']->renderCharset in Backend mode
     * This somewhat hacky work around is currently needed because the conv_case() and convCaseFirst() functions of
     * tslib_cObj rely on those variables to be set
     *
     * @return mixed
     */
    public static function simulateFrontendEnvironment()
    {
        if ('BE' !== TYPO3_MODE) {
            return;
        }
        $tsfeBackup = isset($GLOBALS['TSFE']) ? $GLOBALS['TSFE'] : null;
        $GLOBALS['TSFE'] = new \stdClass();
        // preparing csConvObj
        if (false === is_object($GLOBALS['TSFE']->csConvObj)) {
            if (true === is_object($GLOBALS['LANG'])) {
                $GLOBALS['TSFE']->csConvObj = $GLOBALS['LANG']->csConvObj;
            } else {
                $GLOBALS['TSFE']->csConvObj = GeneralUtility::makeInstance(CharsetConverter::class);
            }
        }
        // preparing renderCharset
        if (false === is_object($GLOBALS['TSFE']->renderCharset)) {
            if (true === is_object($GLOBALS['LANG'])) {
                $GLOBALS['TSFE']->renderCharset = $GLOBALS['LANG']->charSet;
            } else {
                $GLOBALS['TSFE']->renderCharset = 'utf-8';
            }
        }
        return $tsfeBackup;
    }

    /**
     * Resets $GLOBALS['TSFE'] if it was previously changed by simulateFrontendEnvironment()
     *
     * @param mixed $tsfeBackup
     * @return void
     * @see simulateFrontendEnvironment()
     */
    public static function resetFrontendEnvironment($tsfeBackup)
    {
        if ('BE' !== TYPO3_MODE) {
            return;
        }
        $GLOBALS['TSFE'] = $tsfeBackup;
    }
}
