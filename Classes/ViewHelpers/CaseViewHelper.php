<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

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
 * ### Case for SwitchViewHelper
 *
 * Used inside `v:switch` to trigger on specific values.
 *
 * ### Example
 *
 *     <v:switch value="{variable}">
 *         <v:case case="someValue" break="TRUE">
 *             <!-- do whatever, if {variable} == 'someValue' -->
 *         </v:case>
 *         <v:case case="default">
 *             <!-- the case "default" is a reserved keyword which acts as the default case. -->
 *         </v:case>
 *     </v:switch>
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers
 */
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class CaseViewHelper extends AbstractViewHelper {

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('case', 'string', 'Value which triggers this case - reserved name "default" used for default case', TRUE);
		$this->registerArgument('break', 'boolean', 'If TRUE, breaks switch on encountering this case', FALSE, FALSE);
	}

	/**
	 * Renders the case and returns array of content and break-boolean
	 *
	 * @return array
	 */
	public function render() {
		$matchesCase = (boolean) ($this->viewHelperVariableContainer->get('FluidTYPO3\Vhs\ViewHelpers\SwitchViewHelper', 'switchCaseValue') == $this->arguments['case']);
		$mustContinue = $this->viewHelperVariableContainer->get('FluidTYPO3\Vhs\ViewHelpers\SwitchViewHelper', 'switchContinueUntilBreak');
		$isDefault = (boolean) ('default' === $this->arguments['case']);
		if (TRUE === $matchesCase || TRUE == $mustContinue || TRUE === $isDefault) {
			if (TRUE === $this->arguments['break']) {
				$this->viewHelperVariableContainer->addOrUpdate('FluidTYPO3\Vhs\ViewHelpers\SwitchViewHelper', 'switchBreakRequested', TRUE);
			} else {
				$this->viewHelperVariableContainer->addOrUpdate('FluidTYPO3\Vhs\ViewHelpers\SwitchViewHelper', 'switchContinueUntilBreak', TRUE);
			}
			return $this->renderChildren();
		}
		return NULL;
	}

}
