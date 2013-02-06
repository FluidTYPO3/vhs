<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * ### Extension: Loaded (Condition) ViewHelper
 *
 * Condition to check if an extension is loaded.
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers\Extension
 */
class Tx_Vhs_ViewHelpers_Extension_LoadedViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractConditionViewHelper {

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('extensionName', 'string', 'Name of extension that must be loaded in order to evaluate as TRUE, UpperCamelCase', TRUE);
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$extensionName = $this->arguments['extensionName'];
		$extensionKey = t3lib_div::camelCaseToLowerCaseUnderscored($extensionName);
		$isLoaded = t3lib_extMgm::isLoaded($extensionKey);
		if ($isLoaded !== FALSE) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}

}
