<?php
namespace FluidTYPO3\Vhs\Utility;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Xaver Maierhofer <xaver.maierhofer@xwissen.info>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Frontend Simulation Utility
 *
 * Utility to simulate frontend enviroment in backend enviroment.
 *
 * @author Xaver Maierhofer <xaver.maierhofer@xwissen.info>
 * @package Vhs
 * @subpackage Utility
 */
class FrontendSimulationUtility {

	/**
	* Sets the global variables $GLOBALS['TSFE']->csConvObj and $GLOBALS['TSFE']->renderCharset in Backend mode
	* This somewhat hacky work around is currently needed because the conv_case() and convCaseFirst() functions of tslib_cObj rely on those variables to be set
	*
	* @return mixed
	*/
	public static function simulateFrontendEnvironment() {
		if ('BE' !== TYPO3_MODE) {
			return;
		}
		$tsfeBackup = isset($GLOBALS['TSFE']) ? $GLOBALS['TSFE'] : NULL;
		$GLOBALS['TSFE'] = new \stdClass();
		// preparing csConvObj
		if (FALSE === is_object($GLOBALS['TSFE']->csConvObj)) {
			if (TRUE === is_object($GLOBALS['LANG'])) {
				$GLOBALS['TSFE']->csConvObj = $GLOBALS['LANG']->csConvObj;
			} else {
				$GLOBALS['TSFE']->csConvObj = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Charset\\CharsetConverter');
			}
		}
		// preparing renderCharset
		if (FALSE === is_object($GLOBALS['TSFE']->renderCharset)) {
			if (TRUE === is_object($GLOBALS['LANG'])) {
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
	public static function resetFrontendEnvironment($tsfeBackup) {
		if ('BE' !== TYPO3_MODE) {
			return;
		}
		$GLOBALS['TSFE'] = $tsfeBackup;
	}

}
