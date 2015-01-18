<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\String;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Condition: String is lowercase
 *
 * Condition ViewHelper which renders the `then` child if provided
 * string is lowercase. By default only the first letter is tested.
 * To test the full string set $fullString to TRUE.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Condition\String
 */
class IsLowercaseViewHelper extends AbstractConditionViewHelper {

	/**
	 * Render method
	 *
	 * @param string $string
	 * @param boolean $fullString
	 * @return string
	 */
	public function render($string, $fullString = FALSE) {
		if (TRUE === $fullString) {
			$result = ctype_lower($string);
		} else {
			$result = ctype_lower(substr($string, 0, 1));
		}
		if (TRUE === $result) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}

}
