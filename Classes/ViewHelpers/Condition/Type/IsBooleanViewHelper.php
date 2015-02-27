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
 * ### Condition: Type of value is a boolean
 *
 * Condition ViewHelper which renders the `then` child if type of
 * provided value is a boolean.
 *
 * @package Vhs
 * @subpackage ViewHelpers\Condition\Type
 */
class IsBooleanViewHelper extends AbstractConditionViewHelper {

	/**
	 * Render method
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function render($value) {
		if (TRUE === is_bool($value)) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}

}
