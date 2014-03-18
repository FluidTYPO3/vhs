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
 * Once: Cookie
 *
 * Displays nested content or "then" child once, then sets a
 * cookie with $ttl, optionally locked to domain name, which
 * makes the condition return FALSE as long as the cookie exists.
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
class Tx_Vhs_ViewHelpers_Once_CookieViewHelper extends Tx_Vhs_ViewHelpers_Once_AbstractOnceViewHelper {
	/**
	 * @return void
	 */
	protected function storeIdentifier() {
		$identifier = $this->getIdentifier();
		$domain = isset($this->arguments['lockToDomain']) && $this->arguments['lockToDomain'] ? $_SERVER['HTTP_HOST'] : NULL;
		setcookie($identifier, '1', time() + $this->arguments['ttl'], NULL, $domain);
	}

	/**
	 * @return boolean
	 */
	protected function assertShouldSkip() {
		$identifier = $this->getIdentifier();
		return (isset($_COOKIE[$identifier]) === TRUE);
	}

	/**
	 * @return void
	 */
	protected function removeIfExpired() {
		$identifier = $this->getIdentifier();
		$existsInCookie = (isset($_COOKIE[$identifier]) === TRUE);
		if ($existsInCookie === TRUE) {
			unset($_SESSION[$identifier]);
			setcookie($identifier, NULL, time() - 1);
		}
	}

}
