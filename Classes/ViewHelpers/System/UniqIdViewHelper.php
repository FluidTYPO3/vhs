<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Cedric Ziel, <cedric@cedric-ziel.com>
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
 * ### System: Unique ID
 *
 * Returns a unique ID based on PHP's uniqid-function.
 *
 * Comes in useful when handling/generating html-element-IDs
 * for usage with JavaScript.
 *
 * @author Cedric Ziel, <cedric@cedric-ziel.com>
 * @package Vhs
 * @subpackage ViewHelpers\System
 */
class Tx_Vhs_ViewHelpers_System_UniqIdViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @param string $prefix An optional prefix for making sure it's unique across environments
	 * @param boolean $moreEntropy Add some pseudo random strings. Refer to uniqid()'s Reference.
	 * @return string
	 */
	public function render($prefix = '', $moreEntropy = FALSE) {
		$uniqueId = uniqid($prefix, $moreEntropy);
		return $uniqueId;
	}

}
