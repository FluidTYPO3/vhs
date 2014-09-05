<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;
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
