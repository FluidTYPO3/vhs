<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Merges arrays/Traversables $a and $b into an array
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class MergeViewHelper extends AbstractViewHelper {

	use ArrayConsumingViewHelperTrait;

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('a', 'mixed', 'First array/Traversable - if not set, the ViewHelper can be in a chain (inline-notation)', FALSE, NULL);
	}

	/**
	 * Merges arrays/Traversables $a and $b into an array
	 *
	 * @param mixed $b Second array/Traversable
	 * @param boolean $useKeys If TRUE, comparison is done while also observing (and merging) the keys used in each array
	 * @return array
	 */
	public function render($b, $useKeys = TRUE) {
		$a = $this->getArgumentFromArgumentsOrTagContentAndConvertToArray('a');
		$b = $this->arrayFromArrayOrTraversableOrCSV($b, $useKeys);
		$merged = $this->mergeArrays($a, $b);
		return $merged;
	}

}
