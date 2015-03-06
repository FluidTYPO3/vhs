<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

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
		$matchesCase = (boolean) ($this->viewHelperVariableContainer->get('FluidTYPO3\\Vhs\\ViewHelpers\\SwitchViewHelper', 'switchCaseValue') == $this->arguments['case']);
		$mustContinue = $this->viewHelperVariableContainer->get('FluidTYPO3\\Vhs\\ViewHelpers\\SwitchViewHelper', 'switchContinueUntilBreak');
		$isDefault = (boolean) ('default' === $this->arguments['case']);
		if (TRUE === $matchesCase || TRUE == $mustContinue || TRUE === $isDefault) {
			if (TRUE === $this->arguments['break']) {
				$this->viewHelperVariableContainer->addOrUpdate('FluidTYPO3\\Vhs\\ViewHelpers\\SwitchViewHelper', 'switchBreakRequested', TRUE);
			} else {
				$this->viewHelperVariableContainer->addOrUpdate('FluidTYPO3\\Vhs\\ViewHelpers\\SwitchViewHelper', 'switchContinueUntilBreak', TRUE);
			}
			return $this->renderChildren();
		}
		return NULL;
	}

}
