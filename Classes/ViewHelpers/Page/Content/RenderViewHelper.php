<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * ************************************************************* */

/**
 * ViewHelper used to render content elements in Fluid page templates
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @author Dominique Feyer, <dfeyer@ttree.ch>
 * @author Daniel Schöne, <daniel@schoene.it>
 * @package Vhs
 * @subpackage ViewHelpers\Page\Content
 */
class Tx_Vhs_ViewHelpers_Page_Content_RenderViewHelper extends Tx_Vhs_ViewHelpers_Page_Content_GetViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('as', 'string', 'If specified, adds template variable and assumes you manually iterate through {contentRecords}');
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		if (TYPO3_MODE == 'BE') {
			return '';
		}
		$content = $this->getContentRecords();
		if ($this->arguments['as']) {
			$this->templateVariableContainer->add($this->arguments['as'], $content);
			$html = $this->renderChildren();
			$this->templateVariableContainer->remove($this->arguments['as']);
		} else {
			$html = "";
			foreach ($content as $contentRecord) {
				$html .= $contentRecord . LF;
			}
		}
		return $html;
	}

}
