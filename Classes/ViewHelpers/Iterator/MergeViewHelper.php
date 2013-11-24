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
 * Merges arrays/Traversables $a and $b into an array
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class Tx_Vhs_ViewHelpers_Iterator_MergeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var boolean
	 */
	protected $useKeys = TRUE;

	/**
	 * Merges arrays/Traversables $a and $b into an array
	 *
	 * @param mixed $a First array/Traversable
	 * @param mixed $b Second array/Traversable
	 * @param boolean $useKeys If TRUE, comparison is done while also observing (and merging) the keys used in each array
	 * @return array
	 */
	public function render($a, $b, $useKeys = TRUE) {
		$this->useKeys = (boolean) $useKeys;
		$a = $this->ensureIsArray($a);
		$b = $this->ensureIsArray($b);
		$merged = \TYPO3\CMS\Core\Utility\GeneralUtility::array_merge_recursive_overrule($a, $b);
		return $merged;
	}

	/**
	 * @param mixed $candidate
	 * @return array
	 */
	protected function ensureIsArray($candidate) {
		if (TRUE === $candidate instanceof Traversable) {
			return iterator_to_array($candidate, $useKeys);
		}
		if (TRUE === empty($candidate)) {
			return array();
		}
		if (FALSE === is_array($candidate)) {
			return array($candidate);
		}
		return (array) $candidate;
	}

}
