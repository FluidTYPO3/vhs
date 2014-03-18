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
 * ### Condition: Is context Frontend?
 *
 * A condition ViewHelper which renders the `then` child if
 * current context being rendered is FE.
 *
 * ### Examples
 *
 *     <!-- simple usage, content becomes then-child -->
 *     <v:if.context.isFrontend>
 *         Hooray for BE contexts!
 *     </v:if.context.isFrontend>
 *     <!-- extended use combined with f:then and f:else -->
 *     <v:if.context.isFrontend>
 *         <f:then>
 *            Hooray for BE contexts!
 *         </f:then>
 *         <f:else>
 *            Maybe BE, maybe CLI.
 *         </f:else>
 *     </v:if.context.isFrontend>
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\If\Context
 */
class Tx_Vhs_ViewHelpers_If_Context_IsFrontendViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper {

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		if (TYPO3_MODE === 'FE') {
			return $this->renderThenChild();
		}
		return $this->renderElseChild();
	}

}
