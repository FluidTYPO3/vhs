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
 * Merges arrays/Traversables $a and $b into an array
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class Tx_Vhs_ViewHelpers_Iterator_MergeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

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
		$a = \FluidTYPO3\Vhs\Utility\ViewHelperUtility::arrayFromArrayOrTraversableOrCSV($a, $useKeys);
		$b = \FluidTYPO3\Vhs\Utility\ViewHelperUtility::arrayFromArrayOrTraversableOrCSV($b, $useKeys);
		$merged = \TYPO3\CMS\Core\Utility\GeneralUtility::array_merge_recursive_overrule($a, $b);
		return $merged;
	}

}
