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
 * Replaces $substring in $content with $replacement.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Format
 */
class ReplaceViewHelper extends AbstractViewHelper {

	/**
	 * @param string $substring
	 * @param string $content
	 * @param string $replacement
	 * @param integer $count
	 * @param boolean $caseSensitve
	 * @return string
	 */
	public function render($substring, $content = NULL, $replacement = '', $count = NULL, $caseSensitive = TRUE) {
		if (NULL === $content) {
			$content = $this->renderChildren();
		}
		$function = (TRUE === $caseSensitive ? 'str_replace' : 'str_ireplace');
		return str_replace($substring, $replacement, $content, $count);
	}

}
