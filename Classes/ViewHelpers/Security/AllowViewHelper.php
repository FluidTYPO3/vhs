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
 * ### Security: Allow
 *
 * Allows access to the child content based on given arguments.
 * The ViewHelper is a condition based ViewHelper which means it
 * supports the `f:then` and `f:else` child nodes - you can use
 * this behaviour to invert the access (i.e. use f:else in a check
 * if a frontend user is logged in, if you want to hide content
 * from authenticated users):
 *
 *     <v:security.allow anyFrontendUser="TRUE">
 *         <f:then><!-- protected information displayed --></f:then>
 *         <f:else><!-- link to login form displayed --></f:else>
 *     </v:security.allow>
 *
 * Is the mirror opposite of `v:security.deny`.
 *
 * @author Claus Due
 * @package Vhs
 * @subpackage ViewHelpers\Security
 */
class Tx_Vhs_ViewHelpers_Security_AllowViewHelper
extends Tx_Vhs_ViewHelpers_Security_AbstractSecurityViewHelper
implements Tx_Fluid_Core_ViewHelper_Facets_ChildNodeAccessInterface {

	/**
	 * Render allow - i.e. render "then" child if arguments are satisfied
	 *
	 * @return string
	 */
	public function render() {
		$evaluation = $this->evaluateArguments();
		if ($evaluation === TRUE) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}

}
