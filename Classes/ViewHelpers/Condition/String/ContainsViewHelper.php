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
 * ### Condition: String contains substring
 *
 * Condition ViewHelper which renders the `then` child if provided
 * string $haystack contains provided string $needle.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Condition\String
 */
class ContainsViewHelper extends AbstractConditionViewHelper {

	/**
	 * Render method
	 *
	 * @param string $haystack
	 * @param mixed $needle
	 * @return string
	 */
	public function render($haystack, $needle) {
		if (FALSE !== strpos($haystack, $needle)) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}

}
