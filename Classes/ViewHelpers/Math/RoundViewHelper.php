<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * Math: Round
 *
 * Rounds off $a which can be either an array-accessible
 * value (Iterator+ArrayAccess || array) or a raw numeric
 * value.
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers
 */
class Tx_Vhs_ViewHelpers_Math_RoundViewHelper extends Tx_Vhs_ViewHelpers_Math_AbstractSingleMathViewHelper {

	/**
	 * @param mixed $a
	 * @param integer $decimals
	 * @return mixed
	 * @throw Exception
	 */
	public function render($a = NULL, $decimals = 0) {
		if ($a === NULL) {
			$a = $this->renderChildren();
		}
		if ($a === NULL) {
			throw new Exception('Required argument "a" was not supplied', 1237823699);
		}
		$aIsIterable = $this->assertIsArrayOrIterator($a);
		if ($aIsIterable === TRUE) {
			$aCanBeAccessed = $this->assertSupportsArrayAccess($a);
			if ($aCanBeAccessed === FALSE) {
				throw new Exception('Math operation attempted on an inaccessible Iterator. Please implement ArrayAccess or convert the value to an array before calculation', 1351891091);
			}
			foreach ($a as $index => $value) {
				$a[$index] = round($value, $decimals);
			}
			return $a;
		}
		return round($a, $decimals);
	}

}