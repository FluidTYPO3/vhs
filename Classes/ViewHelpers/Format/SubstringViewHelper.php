<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Gets a substring from a string or string-compatible value
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Format
 */
class SubstringViewHelper extends AbstractViewHelper {

	/**
	 * Substrings a string or string-compatible value
	 *
	 * @param string $content Content string to substring
	 * @param integer $start Positive or negative offset
	 * @param integer $length Positive or negative length
	 * @return string
	 */
	public function render($content = NULL, $start = 0, $length = NULL) {
		if (NULL === $content) {
			$content = $this->renderChildren();
		}
		if (NULL !== $length) {
			return substr($content, $start, $length);
		}
		return substr($content, $start);
	}

}
