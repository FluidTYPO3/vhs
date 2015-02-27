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
 * Returns previous element in array $haystack from position of $needle
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class PreviousViewHelper extends ContainsViewHelper {

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		parent::render();
		return $this->getNeedleAtIndex($this->evaluation !== FALSE ? $this->evaluation - 1 : -1);
	}

}
