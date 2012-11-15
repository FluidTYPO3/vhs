<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Andreas Lappe <nd@kaeufli.ch>, kaeufli.ch
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
 ***************************************************************/

/**
 * Abstract ViewHelper around t3lib_utility_Client::getBrowserInfo().
 *
 * @author Andreas Lappe <nd@kaeufli.ch>, kaeufli.ch
 * @package Vhs
 * @subpackage ViewHelpers\Condition
 * @see t3lib_utility_Client::getBrowserInfo() for valid values for both browsers and systems
 * @see Tx_Vhs_ViewHelpers_Condition_BrowserViewHelper for an implementation of this class
 * @see Tx_Vhs_ViewHelpers_Condition_SystemViewHelper for an implementation of this class
 */
abstract class Tx_Vhs_ViewHelpers_Condition_AbstractClientInformationViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractConditionViewHelper {

	/**
	 * @var string
	 */
	private $userAgent = '';

	/**
	 * Return the user-agent
	 *
	 * @return string
	 */
	public function getUserAgent() {
		$userAgent = '';

		if ($this->userAgent) {
			$userAgent = $this->userAgent;
		} else {
			$userAgent = t3lib_div::getIndpEnv('HTTP_USER_AGENT');
		}

		return $userAgent;
	}

	/**
	 * Return all browsers
	 *
	 * @return array
	 */
	public function getBrowsers() {
		$clientInfo = t3lib_utility_Client::getBrowserInfo($this->getUserAgent());

		return $clientInfo['all'];
	}

	/**
	 * Return all systems 
	 *
	 * @return array
	 */
	public function getSystems() {
		$clientInfo = t3lib_utility_Client::getBrowserInfo($this->getUserAgent());

		return $clientInfo['all_systems'];
	}
}
?>