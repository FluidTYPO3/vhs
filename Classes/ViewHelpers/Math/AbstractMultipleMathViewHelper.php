<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;
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
 * Base class: Math ViewHelpers operating on one number or an
 * array of numbers.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers
 */
abstract class AbstractMultipleMathViewHelper extends AbstractSingleMathViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('b', 'mixed', 'Second number or Iterator/Traversable/Array for calculation', TRUE);
	}

	/**
	 * @return mixed
	 * @throw \Exception
	 */
	public function render() {
		$a = $this->getInlineArgument();
		$b = $this->arguments['b'];
		return $this->calculate($a, $b);
	}

	/**
	 * @param mixed $a
	 * @param mixed $b
	 * @return mixed
	 * @throws \Exception
	 */
	protected function calculate($a, $b) {
		if ($b === NULL) {
			throw new \Exception('Required argument "b" was not supplied', 1237823699);
		}
		$aIsIterable = $this->assertIsArrayOrIterator($a);
		$bIsIterable = $this->assertIsArrayOrIterator($b);
		if (TRUE === $aIsIterable) {
			$aCanBeAccessed = $this->assertSupportsArrayAccess($a);
			$bCanBeAccessed = $this->assertSupportsArrayAccess($b);
			if (FALSE === $aCanBeAccessed || (TRUE === $bIsIterable && FALSE === $bCanBeAccessed)) {
				throw new \Exception('Math operation attempted on an inaccessible Iterator. Please implement ArrayAccess or convert the value to an array before calculation', 1351891091);
			}
			foreach ($a as $index => $value) {
				$bSideValue = TRUE === $bIsIterable ? $b[$index] : $b;
				$a[$index] = $this->calculateAction($value, $bSideValue);
			}
			return $a;
		} elseif (TRUE === $bIsIterable) {
			// condition matched if $a is not iterable but $b is.
			throw new \Exception('Math operation attempted using an iterator $b against a numeric value $a. Either both $a and $b, or only $a, must be array/Iterator', 1351890876);
		}
		return $this->calculateAction($a, $b);
	}

}
