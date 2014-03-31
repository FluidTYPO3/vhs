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
 * ### Variable: Isset
 *
 * Renders the `then` child if the variable name given in
 * the `name` argument exists in the template. The value
 * can be zero, NULL or an empty string - but the ViewHelper
 * will still return TRUE if the variable exists.
 *
 * Combines well with dynamic variable names:
 *
 *     <!-- if {variableContainingVariableName} is "foo" this checks existence of {foo} -->
 *     <v:if.var.isset name="{variableContainingVariableName}">...</v:if.var.isset>
 *     <!-- if {suffix} is "Name" this checks existence of "variableName" -->
 *     <v:if.var.isset name="variable{suffix}">...</v:if.var.isset>
 *     <!-- outputs value of {foo} if {bar} is defined -->
 *     {foo -> v:if.var.isset(name: bar)}
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\If\Var
 */
class Tx_Vhs_ViewHelpers_If_Var_IssetViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper {

	/**
	 * Renders else-child or else-argument if variable $name exists
	 *
	 * @param string $name
	 * @return string
	 */
	public function render($name) {
		if ($this->templateVariableContainer->exists($name) === TRUE) {
			return $this->renderThenChild();
		}
		return $this->renderElseChild();
	}

}
