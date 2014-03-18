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
 * ### Will be removed in 2.0
 *
 * Please don't do user agent sniffing. This is bad practice.
 *
 * ### Condition: Client's Browser
 *
 * Condition ViewHelper which renders the `then` child if client's
 * browser matches the `browser` argument
 *
 * ### Examples
 *
 *     <!-- simple usage, content becomes then-child -->
 *     <v:if.client.isBrowser browser="chrome">
 *         Thank you for using Google Chrome!
 *     </v:if.client.isBrowser>
 *     <!-- display a nice warning if not using Chrome -->
 *     <v:if.client.isBrowser browser="chrome">
 *         <f:else>
 *             <div class="alert alert-info">
 *                 <h2 class="alert-header">Please download Google Chrome</h2>
 *                 <p>
 *                     The particular system you are accessing uses features which
 *                     only work in Google Chrome. For the best experience, download
 *                     Chrome here:
 *                     <a href="http://chrome.google.com/">http://chrome.google.com/</a>
 *                 </p>
 *         </f:else>
 *     </v:if.client.isBrowser>
 *
 * @deprecated
 * @author Andreas Lappe <nd@kaeufli.ch>, kaeufli.ch
 * @package Vhs
 * @subpackage ViewHelpers\If\Client
 */
class Tx_Vhs_ViewHelpers_If_Client_IsBrowserViewHelper extends Tx_Vhs_ViewHelpers_If_Client_AbstractClientInformationViewHelper {

	/**
	 * Render method
	 *
	 * @param string $browser
	 * @return string
	 */
	public function render($browser) {
		if (array_key_exists($browser, $this->getBrowsers())) {
			$content = $this->renderThenChild();
		} else {
			$content = $this->renderElseChild();
		}

		return $content;
	}
}
