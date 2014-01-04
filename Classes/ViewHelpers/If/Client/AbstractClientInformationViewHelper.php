<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Andreas Lappe <nd@kaeufli.ch>, kaeufli.ch
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
 * Abstract ViewHelper around \TYPO3\CMS\Core\Utility\ClientUtility::getBrowserInfo().
 *
 * @author Andreas Lappe <nd@kaeufli.ch>, kaeufli.ch
 * @package Vhs
 * @subpackage ViewHelpers\If\Client
 * @see \TYPO3\CMS\Core\Utility\ClientUtility::getBrowserInfo() for valid values for both browsers and systems
 * @see Tx_Vhs_ViewHelpers_Condition_BrowserViewHelper for an implementation of this class
 * @see Tx_Vhs_ViewHelpers_Condition_SystemViewHelper for an implementation of this class
 */
abstract class Tx_Vhs_ViewHelpers_If_Client_AbstractClientInformationViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper {

	/**
	 * @var string
	 */
	protected $userAgent = '';

	/**
	 * Set the user-agent
	 *
	 * @param string $userAgent
	 * @return Tx_Vhs_ViewHelpers_Condition_AbstractClientInformationViewHelper
	 */
	public function setUserAgent($userAgent) {
		$this->userAgent = $userAgent;
		return $this;
	}

	/**
	 * Return the user-agent
	 *
	 * @return string
	 */
	public function getUserAgent() {
		if ($this->userAgent !== '') {
			$userAgent = $this->userAgent;
		} else {
			$userAgent = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('HTTP_USER_AGENT');
		}

		return $userAgent;
	}

	/**
	 * Return all browsers
	 *
	 * @return array
	 */
	public function getBrowsers() {
		$clientInfo = \TYPO3\CMS\Core\Utility\ClientUtility::getBrowserInfo($this->getUserAgent());

		return $clientInfo['all'];
	}

	/**
	 * Return all systems
	 *
	 * @return array
	 */
	public function getSystems() {
		$clientInfo = \TYPO3\CMS\Core\Utility\ClientUtility::getBrowserInfo($this->getUserAgent());

		return $clientInfo['all_systems'];
	}
}
