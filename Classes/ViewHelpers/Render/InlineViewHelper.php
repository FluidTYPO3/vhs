<?php
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

/**
 * ### Render: Inline
 *
 * Render as string containing Fluid as if it were
 * part of the template currently being rendered.
 *
 * Environment (template variables etc.) is cloned
 * but not re-merged after rendering, which means that
 * any and all changes in variables that happen while
 * rendering this inline code will be destroyed after
 * sub-rendering is finished.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Render
 */
class Tx_Vhs_ViewHelpers_Render_InlineViewHelper extends Tx_Vhs_ViewHelpers_Render_AbstractRenderViewHelper {

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('namespaces', 'array', 'Optional additional/overridden namespaces, array("ns" => "Tx_MyExt_ViewHelpers")', FALSE, array());
	}

	/**
	 * Renders an outside string as if it were Fluid code,
	 * using additional (or overridden) namespaces if so
	 * desired.
	 *
	 * @param string $content
	 * @return string
	 */
	public function render($content = NULL) {
		if ($content === NULL) {
			$content = $this->renderChildren();
		}
		$namespaces = $this->getPreparedNamespaces();
		$namespaceHeader = implode(LF, $namespaces);
		foreach ($namespaces as $namespace) {
			$content = str_replace($namespace, '', $content);
		}
		$view = $this->getPreparedClonedView();
		$view->setTemplateSource($namespaceHeader . $content);
		return $this->renderView($view);
	}

}