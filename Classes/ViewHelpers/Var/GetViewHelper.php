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
 * ### Variable: Get
 *
 * ViewHelper used to read the value of a current template
 * variable. Can be used with dynamic indices in arrays:
 *
 *     <v:var.get name="array.{dynamicIndex}" />
 *     <v:var.get name="array.{v:var.get(name: 'arrayOfSelectedKeys.{indexInArray}')}" />
 *     <f:for each="{v:var.get(name: 'object.arrayProperty.{dynamicIndex}')}" as="nestedObject">
 *         ...
 *     </f:for>
 *
 * Or to read names of variables which contain dynamic parts:
 *
 *     <!-- if {variableName} is "Name", outputs value of {dynamicName} -->
 *     {v:var.get(name: 'dynamic{variableName}')}
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Var
 */
class Tx_Vhs_ViewHelpers_Var_GetViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * ### Variable: Get
	 *
	 * Get the variable in $name. Supports dotted-path syntax.
	 *
	 * Can be used to access dynamic variables such as:
	 *
	 *     {v:var.get(name: 'object.arrayProperty.{index}')}
	 *
	 * And can be chained with `v:var.set` to reassign the
	 * output to another variable:
	 *
	 *     {v:var.get(name: 'myArray.{index}') -> v:var.set(name: 'myVar')}
	 *
	 * If your target object is an array with unsequential yet
	 * numeric indices (e.g. {123: 'value1', 513: 'value2'},
	 * commonly seen in reindexed UID map arrays) use
	 * `useRawIndex="TRUE"` to indicate you do not want your
	 * array/QueryResult/Iterator to be accessed by locating
	 * the Nth element - which is the default behavior.
	 *
	 * ```warning
	 * Do not try `useRawKeys="TRUE"` on QueryResult or
	 * ObjectStorage unless you are fully aware what you are
	 * doing. These particular types require an unpredictable
	 * index value - the SPL object hash value - when accessing
	 * members directly. This SPL indexing and the very common
	 * occurrences of QueryResult and ObjectStorage variables
	 * in templates is the very reason why `useRawKeys` by
	 * default is set to `FALSE`.
	 * ```
	 *
	 * @param string $name
	 * @param boolean $useRawKeys
	 * @return mixed
	 */
	public function render($name, $useRawKeys = FALSE) {
		if (strpos($name, '.') === FALSE) {
			if ($this->templateVariableContainer->exists($name) === TRUE) {
				return $this->templateVariableContainer->get($name);
			}
		} else {
			$segments = explode('.', $name);
			$templateVariableRootName = $lastSegment = array_shift($segments);
			if ($this->templateVariableContainer->exists($templateVariableRootName)) {
				$templateVariableRoot = $this->templateVariableContainer->get($templateVariableRootName);
				if (TRUE === $useRawKeys) {
					return \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getPropertyPath($templateVariableRoot, implode('.', $segments));
				}
				try {
					$value = $templateVariableRoot;
					foreach ($segments as $segment) {
						if (ctype_digit($segment)) {
							$segment = intval($segment);
							$index = 0;
								// Note: this loop approach is not a stupid solution. If you doubt this,
								// attempt to feth a number at a numeric index from ObjectStorage ;)
							foreach ($value as $possibleValue) {
								if ($index === $segment) {
									$value = $possibleValue;
									break;
								}
								++ $index;
							}
							continue;
						}
						$value = \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getProperty($value, $segment);
					}
					return $value;
				} catch (Exception $e) {
					return NULL;
				}
			}
		}
		return NULL;
	}

}
