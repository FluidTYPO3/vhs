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
 * Math: Modulo
 * Perform modulo on $input. Returns the same type as $input,
 * i.e. if given an array, will transform each member and return
 * the result. Supports array and Iterator (in the following
 * descriptions "array" means both these types):
 *
 * If $a and $b are both arrays of the same size then modulo is
 * performed on $a using members of $b, by their index (so these
 * must match in both arrays).
 *
 * If $a is an array and $b is a number then modulo is performed
 * on $a using $b for each calculation.
 *
 * If $a and $b are both numbers simple modulo is performed.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Math
 */
class Tx_Vhs_ViewHelpers_Math_ModuloViewHelper extends Tx_Vhs_ViewHelpers_Math_AbstractMultipleMathViewHelper {

	/**
	 * @param mixed $a
	 * @param mixed $b
	 * @return integer
	 */
	protected function calculateAction($a, $b) {
		return $a % $b;
	}

}
