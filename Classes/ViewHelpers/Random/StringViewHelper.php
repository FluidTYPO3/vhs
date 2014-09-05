<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Random;

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
 * ### Random: String Generator
 *
 * Use either `minimumLength` / `maximumLength` or just `length`.
 *
 * Specify the characters which can be randomized using `characters`.
 *
 * Has built-in insurance that first character of random string is
 * an alphabetic character (allowing safe use as DOM id for example).
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Random
 */
class StringViewHelper extends AbstractViewHelper {

	/**
	 * @param integer $length
	 * @param integer $minimumLength
	 * @param integer $maximumLength
	 * @param string $characters
	 * @return string
	 */
	public function render($length = NULL, $minimumLength = 32, $maximumLength = 32, $characters = '0123456789abcdef') {
		$minimumLength = intval($minimumLength);
		$maximumLength = intval($maximumLength);
		$length = ($minimumLength != $maximumLength ? rand($minimumLength, $maximumLength) : ($length !== NULL ? $length : $minimumLength));
		$string = '';
		for ($i = 0; $i < $length && $length > 0; $i++) {
			$randomIndex = rand(0, strlen($characters));
			$string .= $characters{$randomIndex};
		}
		$characters = preg_replace('/([^a-z]+)/i', '', $characters);
		$randomIndex = rand(0, strlen($characters) - 1);
		$string{0} = $characters{$randomIndex};
		return $string;
	}

}
