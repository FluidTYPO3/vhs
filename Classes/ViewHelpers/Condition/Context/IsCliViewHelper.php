<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Context;

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
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Condition: Is context CLI?
 *
 * A condition ViewHelper which renders the `then` child if
 * current context being rendered is CLI.
 *
 * ### Examples
 *
 *     <!-- simple usage, content becomes then-child -->
 *     <v:condition.context.isCli>
 *         Hooray for CLI contexts!
 *     </v:condition.context.isCli>
 *     <!-- extended use combined with f:then and f:else -->
 *     <v:condition.context.isCli>
 *         <f:then>
 *            Hooray for CLI contexts!
 *         </f:then>
 *         <f:else>
 *            Maybe BE, maybe FE.
 *         </f:else>
 *     </v:condition.context.isCli>
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Condition\Context
 */
class IsCliViewHelper extends AbstractConditionViewHelper {

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		if (TRUE === defined('TYPO3_cliMode')) {
			return $this->renderThenChild();
		}
		return $this->renderElseChild();
	}

}
