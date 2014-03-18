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
 * Formats or gets detected substrings by regular expression.
 * Returns matches if `return="TRUE"`, otherwise replaces any
 * occurrences of `$pattern`.
 * simply returns
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Format
 */
class Tx_Vhs_ViewHelpers_Format_RegularExpressionViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @param string $pattern The regular expression pattern to search for
	 * @param string $replacement The desired value to insert instead of detected matches
	 * @param string $subject The subject in which to perform replacements/detection; taken from tag content if not specified.
	 * @param boolean $return
	 * @return mixed
	 */
	public function render($pattern, $replacement, $subject = NULL, $return = FALSE) {
		if (NULL === $subject) {
			$subject = $this->renderChildren();
		}
		if (TRUE === $return) {
			$returnValue = array();
			preg_match($pattern, $subject, $returnValue);
		} else {
			$returnValue = preg_replace($pattern, $replacement, $subject);
		}
		return $returnValue;
	}

}
