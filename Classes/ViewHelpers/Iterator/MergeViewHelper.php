<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ViewHelperUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Merges arrays/Traversables $a and $b into an array
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class MergeViewHelper extends AbstractViewHelper {

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
		$a = ViewHelperUtility::arrayFromArrayOrTraversableOrCSV($a, $useKeys);
		$b = ViewHelperUtility::arrayFromArrayOrTraversableOrCSV($b, $useKeys);
		$merged = GeneralUtility::array_merge_recursive_overrule($a, $b);
		return $merged;
	}

}
