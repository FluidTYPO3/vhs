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
 * string is uppercase. By default only the first letter is tested.
 * To test the full string set $fullString to TRUE.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Condition\String
 */
class IsUppercaseViewHelper extends AbstractConditionViewHelper {

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('string', 'string', 'string to check', TRUE);
		$this->registerArgument('fullString', 'string', 'need', FALSE, FALSE);
	}

	/**
	 * This method decides if the condition is TRUE or FALSE. It can be overriden in extending viewhelpers to adjust functionality.
	 *
	 * @param array $arguments ViewHelper arguments to evaluate the condition for this ViewHelper, allows for flexiblity in overriding this method.
	 * @return bool
	 */
	static protected function evaluateCondition($arguments = NULL) {
		if (TRUE === $arguments['fullString']) {
			$result = ctype_upper($arguments['string']);
		} else {
			$result = ctype_upper(substr($arguments['string'], 0, 1));
		}
		return TRUE === $result;
	}

}
