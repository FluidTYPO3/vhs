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
 * ### Variable: Set
 *
 * Sets a single variable in the TemplateVariableContainer
 * scope. The variable then becomes accessible as {var}.
 *
 * Combines well with `v:var.get` to set shorter variable
 * names referencing dynamic variables, such as:
 *
 *     <v:var.set name="myObject" value="{v:var.get(name: 'arrayVariable.{offset}')}" />
 *     <!-- If {index} == 4 then {myObject} is now == {arrayVariable.4} -->
 *     {myObject.name} <!-- corresponds to {arrayVariable.4.name} -->
 *
 * Note that `{arrayVariable.{offset}.name}` is not possible
 * due to the way Fluid parses nodes; the above piece of
 * code would try reading `arrayVariable.{offset}.name`
 * as a variable actually called "arrayVariable.{offset}.name"
 * rather than the correct `arrayVariable[offset][name]`.
 *
 * In many ways this ViewHelper works like `f:alias`
 * with one exception: in `f:alias` the variable only
 * becomes accessible in the tag content, whereas `v:var.set`
 * inserts the variable in the template and leaves it there
 * (it "leaks" the variable).
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers\Var
 */
class Tx_Vhs_ViewHelpers_Var_SetViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Set (override) the variable in $name.
	 *
	 * Why is $value first? In order to support this, for example:
	 *
	 * {value -> vhs:format.plaintext() -> vhs:var.set(name: 'myVariable')}
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function render($name, $value = NULL) {
		if ($value === NULL) {
			$value = $this->renderChildren();
		}
		if ($this->templateVariableContainer->exists($name) === TRUE) {
			$this->templateVariableContainer->remove($name);
		}
		$this->templateVariableContainer->add($name, $value);
		return NULL;
	}

}
