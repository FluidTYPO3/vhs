<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\String;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
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
	 * Render method
	 *
	 * @param string $string
	 * @param boolean $fullString
	 * @return string
	 */
	public function render($string, $fullString = FALSE) {
		if (TRUE === $fullString) {
			$result = ctype_upper($string);
		} else {
			$result = ctype_upper(substr($string, 0, 1));
		}
		if (TRUE === $result) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}

}
