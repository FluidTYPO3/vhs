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
 * Math: Division
 *
 * Performs division of $a using $b. A can be an array and $b a
 * number, in which case each member of $a gets divided by $b.
 * If both $a and $b are arrays, each member of $a is summed
 * against the corresponding member in $b compared using index.
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers
 */
class Tx_Vhs_ViewHelpers_Math_DivisionViewHelper extends Tx_Vhs_ViewHelpers_Math_AbstractSingleMathViewHelper {

	/**
	 * @param mixed $a
	 * @param mixed $b
	 * @return mixed
	 * @throw Exception
	 */
	public function render($a = NULL, $b = NULL) {
		if ($a === NULL) {
			$a = $this->renderChildren();
		}
		if ($a === NULL) {
			throw new Exception('Required argument "a" was not supplied', 1237823699);
		}
		if ($b === NULL) {
			throw new Exception('Required argument "a" was not supplied', 1237823699);
		}
		$aIsIterable = $this->assertIsArrayOrIterator($a);
		$bIsIterable = $this->assertIsArrayOrIterator($b);
		if ($aIsIterable === TRUE) {
			if ($b === NULL) {
				return array_sum($a);
			}
			$aCanBeAccessed = $this->assertSupportsArrayAccess($a);
			$bCanBeAccessed = $this->assertSupportsArrayAccess($b);
			if ($aCanBeAccessed === FALSE || $bCanBeAccessed === FALSE) {
				throw new Exception('Math operation attempted on an inaccessible Iterator. Please implement ArrayAccess or convert the value to an array before calculation', 1351891091);
			}
			foreach ($a as $index => $value) {
				$bSideValue = ($bIsIterable === TRUE ? $b[$index] : $b);
				$a[$index] = ($bSideValue <> 0 ? $value / $bSideValue : $value);
			}
			return $a;
		} elseif ($bIsIterable === TRUE) {
			// condition matched if $a is not iterable but $b is.
			throw new Exception('Math operation attempted using an iterator $b against a numeric value $a. Either both $a and $b, or only $a, must be array/Iterator', 1351890876);
		}
		return ($b <> 0 ? $a / $b : $a);
	}

}
