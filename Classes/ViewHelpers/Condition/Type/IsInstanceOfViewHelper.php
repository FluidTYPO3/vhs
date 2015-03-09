<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Type;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Condition: Value is an instance of a class
 *
 * Condition ViewHelper which renders the `then` child if provided
 * value is an instance of provided class name.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Condition\Type
 */
class IsInstanceOfViewHelper extends AbstractConditionViewHelper {

	/**
	 * Render method
	 *
	 * @param mixed $value
	 * @param string $class
	 * @return string
	 */
	public function render($value, $class) {
		if (TRUE === $value instanceof $class) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}

}
