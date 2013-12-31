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
 * Math: Median
 *
 * Gets the median value from an array of numbers. If there
 * is an odd number of numbers the middle value is returned.
 * If there is an even number of numbers an average of the
 * two middle numbers is returned.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Math
 */
class Tx_Vhs_ViewHelpers_Math_MedianViewHelper extends Tx_Vhs_ViewHelpers_Math_AbstractSingleMathViewHelper {

	/**
	 * @return mixed
	 * @throw Exception
	 */
	public function render() {
		$a = $this->getInlineArgument();
		$aIsIterable = $this->assertIsArrayOrIterator($a);
		if ($aIsIterable) {
			$a = $this->convertTraversableToArray($a);
			sort($a, SORT_NUMERIC);
			$size = count($a);
			$midpoint = $size / 2;
			if (1 === $size % 2) {
				return $a[$midpoint];
			}
			$candidates = array_slice($a, floor($midpoint) - 1, 2);
			return array_sum($candidates) / 2;
		}
		return $a;
	}

}
