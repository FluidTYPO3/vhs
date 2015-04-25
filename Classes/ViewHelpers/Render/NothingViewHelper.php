<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * ### Render: Nothing

 * Does not output the rendering result of its children.

 * Can be used to prevent any output when including a partial
 * with v:asset definitions like:
 *
 *     <v:render.nothing>
 *         <v:asset.style ... />
 *         <-- HTML comments or any other content like linebreaks
 *         will not be rendered. -->
 *         <v:asset.style ... />
 *     </v:render.nothing>
 *
 *
 * @author Benjamin Beck <beck@beckdigitalemedien.de>
 * @package Vhs
 * @subpackage ViewHelpers\Render
 */
class NothingViewHelper extends AbstractRenderViewHelper {

	/**
	 * Renders nothing (prevents rendering)
	 *
	 * @param string $content
	 * @return string
	 */
	public function render($content = NULL) {
		$this->renderChildren();
		return;
	}

}
