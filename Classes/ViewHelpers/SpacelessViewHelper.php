<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * This Viewhelper is highly inspired by Twig's "spaceless Tag"
 *
 * @see http://twig.sensiolabs.org/doc/tags/spaceless.html
 */
class SpacelessViewHelper extends AbstractViewHelper {

	/**
	 * Removes whitespaces between HTML tags.
	 *
	 * <vhs:spaceless>
	 *     <div>
	 *         <strong>foo</strong>
	 *     </div>
	 * </vhs:spaceless>
	 *
	 * Output will be <div><strong>foo</strong></div>.
	 *
	 * @return string
	 */
	public function render() {
		$result = trim(
			preg_replace(
				'/>\s+</',
				'><',
				$this->renderChildren()
			)
		);

		return $result;
	}

}
