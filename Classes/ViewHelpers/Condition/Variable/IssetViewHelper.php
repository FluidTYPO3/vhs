<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

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
 *     <v:condition.variable.isset name="{variableContainingVariableName}">...</v:condition.variable.isset>
 *     <!-- if {suffix} is "Name" this checks existence of "variableName" -->
 *     <v:condition.variable.isset name="variable{suffix}">...</v:condition.variable.isset>
 *     <!-- outputs value of {foo} if {bar} is defined -->
 *     {foo -> v:condition.variable.isset(name: bar)}
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Condition\Variable
 */
class IssetViewHelper extends AbstractConditionViewHelper {

	/**
	 * Renders else-child or else-argument if variable $name exists
	 *
	 * @param string $name
	 * @return string
	 */
	public function render($name) {
		if (TRUE === $this->templateVariableContainer->exists($name)) {
			return $this->renderThenChild();
		}
		return $this->renderElseChild();
	}

}
