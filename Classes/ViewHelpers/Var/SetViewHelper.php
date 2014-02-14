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
 * If $name contains a dot, VHS will attempt to load the object
 * stored under the named used as the first segment part and
 * set the value at the remaining path. E.g.
 * `{value -> v:var.set(name: 'object.property.subProperty')}`
 * would attempt to load `{object}` first, then set
 * `property.subProperty` on that object/array using
 * ObjectAccess::setPropertyPath(). If `{object}` is not
 * an object or an array, the variable will not be set. Please
 * note: Extbase does not currently support setting variables
 * deeper than two levels, meaning a `name` of fx `foo.bar.baz`
 * will be ignored. To set values deeper than two levels you
 * must first extract the second-level object then set the
 * value on that object.
 *
 * Using as `{value -> v:var.set(name: 'myVar')}` makes `{myVar}` contain
 * `{value}`.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Var
 */
class Tx_Vhs_ViewHelpers_Var_SetViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Set (override) the variable in $name.
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function render($name, $value = NULL) {
		if ($value === NULL) {
			$value = $this->renderChildren();
		}
		if (FALSE === strpos($name, '.')) {
			if ($this->templateVariableContainer->exists($name) === TRUE) {
				$this->templateVariableContainer->remove($name);
			}
			$this->templateVariableContainer->add($name, $value);
		} elseif (1 == substr_count($name, '.')) {
			$parts = explode('.', $name);
			$objectName = array_shift($parts);
			$path = implode('.', $parts);
			if (FALSE === $this->templateVariableContainer->exists($objectName)) {
				return NULL;
			}
			$object = $this->templateVariableContainer->get($objectName);
			try {
				\TYPO3\CMS\Extbase\Reflection\ObjectAccess::setProperty($object, $path, $value);
				// Note: re-insert the variable to ensure unreferenced values like arrays also get updated
				$this->templateVariableContainer->remove($objectName);
				$this->templateVariableContainer->add($objectName, $object);
			} catch (Exception $error) {
				return NULL;
			}
		}
		return NULL;
	}

}
