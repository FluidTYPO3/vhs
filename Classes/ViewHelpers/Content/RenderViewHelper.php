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
 * ************************************************************* */

/**
 * ViewHelper used to render content elements in Fluid page templates
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @author Dominique Feyer, <dfeyer@ttree.ch>
 * @author Daniel Schöne, <daniel@schoene.it>
 * @author Björn Fromme, <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Content
 */
class Tx_Vhs_ViewHelpers_Content_RenderViewHelper extends Tx_Vhs_ViewHelpers_Content_AbstractContentViewHelper {

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

		$as = $this->arguments['as'];
		if (TRUE === empty($as)) {
			return implode(LF, $content);
		}

		$variables = array($as => $content);
		$output = Tx_Vhs_Utility_ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);
		return $output;
	}

}
