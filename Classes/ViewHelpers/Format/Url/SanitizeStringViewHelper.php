<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * ************************************************************* */

/**
 * URL text segment sanitizer. Sanitizes the content into a
 * valid URL segment value which is usable in an URL without
 * further processing. For example, the text "I am Mr. Brown,
 * how are you?" becomes "i-am-mr-brown-how-are-you".
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers\Format
 */
class Tx_Vhs_ViewHelpers_Format_Url_SanitizeStringViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @param string $string
	 * @return string
	 */
	public function render($string = NULL) {
		if ($string === NULL) {
			$string = $this->renderChildren();
		}
		$pattern = '/([^a-z0-9\-]){1,}/i';
		$string = preg_replace($pattern, '-', $string);
		$string = strtolower($string);
		return trim($string, '-');
	}

}
