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
 * ### Render: ASCII Character
 *
 * Renders a single character identified by its charset number.
 *
 * For example: `<v:render.character ascii="10" /> renders a UNIX linebreak
 * as does {v:render.character(ascii: 10)}. Can be used in combination with
 * `v:iterator.loop` to render sequences or repeat the same character:
 *
 *     {v:render.ascii(ascii: 10) -> v:iterator.loop(count: 5)}
 *
 * And naturally you can feed any integer variable or ViewHelper return value
 * into the `ascii` parameter throught `renderChildren` to allow chaining:
 *
 *     {variableWithAsciiInteger -> v:render.ascii()}
 *
 * And arrays are also supported - they will produce a string of characters
 * from each number in the array:
 *
 *     {v:render.ascii(ascii: {0: 13, 1: 10})}
 *
 * Will produce a Windows line break, \r\n
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Render
 */
class Tx_Vhs_ViewHelpers_Render_AsciiViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @param mixed $ascii
	 * @return string
	 */
	public function render($ascii = NULL) {
		if (NULL === $ascii) {
			$ascii = $this->renderChildren();
		}
		if (TRUE === ctype_digit($ascii)) {
			return chr($ascii);
		}
		if (TRUE === is_array($ascii) || TRUE === $ascii instanceof Traversable) {
			$string = '';
			foreach ($ascii as $characterNumber) {
				$string .= chr($characterNumber);
			}
			return $string;
		}
		return '';
	}

}
