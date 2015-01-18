<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media\Image;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Returns the height of the provided image file in pixels
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Media\Image
 */
class HeightViewHelper extends AbstractImageInfoViewHelper {

	/**
	 * @return int
	 */
	public function render() {
		$info = $this->getInfo();
		return (TRUE === isset($info['height']) ? $info['height'] : 0);
	}

}
