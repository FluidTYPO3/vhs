<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Random;

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
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Random: Number Generator
 *
 * Generates a random number. The default minimum number is
 * set to 100000 in order to generate a longer integer string
 * representation. Decimal values can be generated as well.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Random
 */
class NumberViewHelper extends AbstractViewHelper {

	/**
	 * @param integer $minimum Minimum number - defaults to 100000 (max is 999999 making lengths uniform with adequate entropy)
	 * @param integer $maximum Maximum number - defaults to 999999 (min is 100000 making lengths uniform with adequate entropy)
	 * @param integer $minimumDecimals Minimum number of also randomized decimal digits to add to number
	 * @param integer $maximumDecimals Maximum number of also randomized decimal digits to add to number
	 * @return float
	 */
	public function render($minimum = 100000, $maximum = 999999, $minimumDecimals = 0, $maximumDecimals = 0) {
		$natural = rand($minimum, $maximum);
		if (0 === (integer) $minimumDecimals && 0 === (integer) $maximumDecimals) {
			return $natural;
		}
		$decimals = array_fill(0, rand($minimumDecimals, $maximumDecimals), 0);
		$decimals = array_map(function () {
			return rand(0, 9);
		}, $decimals);
		return floatval($natural . '.' . implode('', $decimals));
	}

}
