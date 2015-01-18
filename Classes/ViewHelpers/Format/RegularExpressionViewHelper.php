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
 * Formats or gets detected substrings by regular expression.
 * Returns matches if `return="TRUE"`, otherwise replaces any
 * occurrences of `$pattern`.
 * simply returns
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Format
 */
class RegularExpressionViewHelper extends AbstractViewHelper {

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
