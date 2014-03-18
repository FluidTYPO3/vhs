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
 * Math: Maximum
 *
 * Gets the highest number in array $a or the highest
 * number of numbers $a and $b.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Math
 */
class Tx_Vhs_ViewHelpers_Math_MaximumViewHelper extends Tx_Vhs_ViewHelpers_Math_AbstractMultipleMathViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->overrideArgument('b', 'mixed', 'Second number or Iterator/Traversable/Array for calculation', FALSE, NULL);
	}

	/**
	 * @return mixed
	 * @throw Exception
	 */
	public function render() {
		$a = $this->getInlineArgument();
		$b = $this->arguments['b'];
		$aIsIterable = $this->assertIsArrayOrIterator($a);
		if ($aIsIterable && $b === NULL) {
			$a = $this->convertTraversableToArray($a);
			return max($a);
		}
		return $this->calculate($a, $b);
	}

	/**
	 * @param mixed $a
	 * @param mixed $b
	 * @return mixed
	 */
	protected function calculateAction($a, $b) {
		return max($a, $b);
	}

}
