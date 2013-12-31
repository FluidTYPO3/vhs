<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
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
 * Once: Session
 *
 * Displays nested content or "then" child once per session.
 *
 * "Once"-style ViewHelpers are purposed to only display their
 * nested content once per XYZ, where the XYZ depends on the
 * specific type of ViewHelper (session, cookie etc).
 *
 * In addition the ViewHelper is a ConditionViewHelper, which
 * means you can utilize the f:then and f:else child nodes as
 * well as the "then" and "else" arguments.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Once
 */
class Tx_Vhs_ViewHelpers_Once_SessionViewHelper extends Tx_Vhs_ViewHelpers_Once_AbstractOnceViewHelper {

	/**
	 * @return string
	 */
	public function render() {
		if (!session_id()) {
			session_start();
		}
		return parent::render();
	}

	/**
	 * @return void
	 */
	protected function storeIdentifier() {
		$identifier = $this->getIdentifier();
		$index = get_class($this);
		if (is_array($_SESSION[$index]) === FALSE) {
			$_SESSION[$index] = array();
		}
		$_SESSION[$index][$identifier] = TRUE;
	}

	/**
	 * @return boolean
	 */
	protected function assertShouldSkip() {
		$identifier = $this->getIdentifier();
		$index = get_class($this);
		return (isset($_SESSION[$index][$identifier]) === TRUE);
	}

	/**
	 * @return void
	 */
	protected function removeIfExpired() {
		$identifier = $this->getIdentifier();
		$index = get_class($this);
		$existsInSession = (isset($_SESSION[$index]) === TRUE && isset($_SESSION[$index][$identifier]) === TRUE);
		if ($existsInSession === TRUE && $_SESSION[$index][$identifier] <= time() - $this->arguments['ttl']) {
			unset($_SESSION[$index][$identifier]);
		}
	}

}
