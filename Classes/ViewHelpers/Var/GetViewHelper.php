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
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers\Var
 */
class Tx_Vhs_ViewHelpers_Var_GetViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Get the variable in $name.
	 *
	 * @param string $name
	 * @throws Exception
	 * @return mixed
	 */
	public function render($name) {
		if (strpos($name, '.') === FALSE) {
			if ($this->templateVariableContainer->exists($name) === TRUE) {
				return $this->templateVariableContainer->get($name);
			}
		} else {
			$segments = explode('.', $name);
			$templateVariableRootName = $lastSegment = array_shift($segments);
			if ($this->templateVariableContainer->exists($templateVariableRootName)) {
				$templateVariableRoot = $this->templateVariableContainer->get($templateVariableRootName);
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
						$value = Tx_Extbase_Reflection_ObjectAccess::getProperty($value, $segment);
						$lastSegment = $segment;
					}
					return $value;
				} catch (Exception $e) {
					return NULL;
					throw $e;
				}
			}
		}
		return NULL;
	}

}
