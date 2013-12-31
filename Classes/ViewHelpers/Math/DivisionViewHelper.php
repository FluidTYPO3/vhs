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
 * Math: Division
 *
 * Performs division of $a using $b. A can be an array and $b a
 * number, in which case each member of $a gets divided by $b.
 * If both $a and $b are arrays, each member of $a is summed
 * against the corresponding member in $b compared using index.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Math
 */
class Tx_Vhs_ViewHelpers_Math_DivisionViewHelper extends Tx_Vhs_ViewHelpers_Math_AbstractMultipleMathViewHelper {

	/**
	 * @param mixed $a
	 * @param mixed $b
	 * @return mixed
	 */
	protected function calculateAction($a, $b) {
		return ($b <> 0 ? $a / $b : $a);
	}

}
