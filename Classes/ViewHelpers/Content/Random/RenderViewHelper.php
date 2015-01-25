<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Content\Random;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * ViewHelper for rendering a random content element in Fluid page templates
 *
 * @author Björn Fromme, <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Content\Random
 */
class RenderViewHelper extends GetViewHelper {

	/**
	 * Render method
	 *
	 * @return mixed
	 */
	public function render() {
		$contentRecords = (array) parent::render();
		$html = implode(LF, $contentRecords);
		return $html;
	}

}
