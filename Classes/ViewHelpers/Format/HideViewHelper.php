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
 * Hides output from browser, but still renders tag content
 * which means any ViewHelper inside the tag content still
 * gets processed.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Format
 */
class HideViewHelper extends AbstractViewHelper {

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('disabled', 'boolean', 'If TRUE, renders content - use to quickly enable/disable Fluid code', FALSE, FALSE);
	}

	/**
	 * @return string
	 */
	public function render() {
		if (TRUE === (boolean) $this->arguments['disabled']) {
			return $this->renderChildren();
		} else {
			$this->renderChildren();
		}
		return NULL;
	}

}
