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
 * ### Format: Prepend string content
 *
 * Prepends one string on another. Although this task is very
 * easily done in standard Fluid - i.e. {add}{subject} - this
 * ViewHelper makes advanced chained inline processing possible:
 *
 *     <!-- Adds 1H to DateTime, formats using timestamp input which requires prepended @ -->
 *     {dateTime.timestamp
 *         -> v:math.sum(b: 3600)
 *         -> v:format.prepend(add: '@')
 *         -> v:format.date(format: 'Y-m-d H:i')}
 *     <!-- You don't have to break the syntax into lines; done here for display only -->
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Format
 */
class PrependViewHelper extends AbstractViewHelper {

	/**
	 * @param string $add
	 * @param string $subject
	 * @return string
	 */
	public function render($add, $subject = NULL) {
		if (NULL === $subject) {
			$subject = $this->renderChildren();
		}
		return $add . $subject;
	}

}