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
 * Base class: Math ViewHelpers operating on one number or an
 * array of numbers.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers
 */
abstract class Tx_Vhs_ViewHelpers_Math_AbstractSingleMathViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('a', 'mixed', 'First number for calculation', FALSE, NULL, TRUE);
		$this->registerArgument('fail', 'boolean', 'If TRUE, throws an Exception if argument "a" is not specified and no child content or inline argument is found. Usually okay to use a NULL value (as integer zero).', FALSE, FALSE);
	}

	/**
	 * @param mixed $subject
	 * @return boolean
	 */
	protected function assertIsArrayOrIterator($subject) {
		return (is_array($subject) === TRUE || $subject instanceof Iterator === TRUE);
	}

	/**
	 * @param $subject
	 * @return boolean
	 */
	protected function assertSupportsArrayAccess($subject) {
		return (is_array($subject) === TRUE || ($subject instanceof Iterator === TRUE && $subject instanceof ArrayAccess === TRUE));
	}

	/**
	 * @param array|Traversable $traversable
	 * @throws Exception
	 * @return array
	 */
	protected function convertTraversableToArray($traversable) {
		if ($this->assertIsArrayOrIterator($traversable) === FALSE) {
			throw new Exception('Attempt to convert non-traversable object to array', 1353442738);
		}
		$array = array();
		foreach ($traversable as $key => $value) {
			$array[$key] = $value;
		}
		return $array;
	}

	/**
	 * @return mixed
	 * @throw Exception
	 */
	public function render() {
		$a = $this->getInlineArgument();
		return $this->calculate($a);
	}

	/**
	 * @throws Exception
	 * @return mixed
	 */
	protected function getInlineArgument() {
		$a = $this->renderChildren();
		if (NULL === $a && TRUE === isset($this->arguments['a'])) {
			$a = $this->arguments['a'];
		}
		if (NULL === $a && TRUE === (boolean) $this->arguments['fail']) {
			throw new Exception('Required argument "a" was not supplied', 1237823699);
		}
		return $a;
	}

	/**
	 * @param mixed $a
	 * @throws Exception
	 * @return mixed
	 */
	protected function calculate($a) {
		$aIsIterable = $this->assertIsArrayOrIterator($a);
		if ($aIsIterable === TRUE) {
			$aCanBeAccessed = $this->assertSupportsArrayAccess($a);
			if ($aCanBeAccessed === FALSE) {
				throw new Exception('Math operation attempted on an inaccessible Iterator. Please implement ArrayAccess or convert the value to an array before calculation', 1351891091);
			}
			foreach ($a as $index => $value) {
				$a[$index] = $this->calculateAction($value);
			}
			return $a;
		}
		return $this->calculateAction($a);
	}

}
