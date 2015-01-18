<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Condition\Iterator\ContainsViewHelper;

/**
 * Searches $haystack for index of $needle, returns -1 if $needle
 * is not in $haystack
 *
 * @author Claus Due
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class IndexOfViewHelper extends ContainsViewHelper {

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		parent::render();
		if (FALSE !== $this->evaluation) {
			return intval($this->evaluation);
		}
		return -1;
	}

}
