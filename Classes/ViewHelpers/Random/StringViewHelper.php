<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Random;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

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
		for ($i = 0; $i < $length; $i++) {
			$randomIndex = mt_rand(0, strlen($characters) - 1);
			$string .= $characters{$randomIndex};
		}
		return $string;
	}

}
