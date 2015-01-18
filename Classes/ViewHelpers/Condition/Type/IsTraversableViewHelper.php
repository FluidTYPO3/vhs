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
 * ### Condition: Value implements interface Traversable
 *
 * Condition ViewHelper which renders the `then` child if provided
 * value implements interface Traversable
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Condition\Type
 */
class IsTraversableViewHelper extends AbstractConditionViewHelper {

	/**
	 * Render method
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function render($value) {
		if (TRUE === $value instanceof \Traversable) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}

}
