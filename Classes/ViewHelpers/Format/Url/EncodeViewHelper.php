<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format\Url;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Urlencodes the provided string
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Format
 */
class EncodeViewHelper extends AbstractViewHelper {

	/**
	 * @param string $content
	 * @return string
	 */
	public function render($content = NULL) {
		if (NULL === $content) {
			$content = $this->renderChildren();
		}
		return rawurlencode($content);
	}

}
