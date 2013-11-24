<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Cedric Ziel <cedric@cedric-ziel.com>, Internetdienstleistungen & EDV
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
 * ### TypolinkViewhelper
 *
 * Renders a link with the TypoLink function.
 * Can be used with the LinkWizard
 *
 * ### Examples
 *
 *     <!-- tag -->
 *     <v:link.typolink configuration="{typoLinkConfiguration}" />
 *     <v:link.typolink configuration="{object}">My LinkText</v:link.typolink>
 *
 * @author Cedric Ziel <cedric@cedric-ziel.com>, Cedric Ziel - Internetdienstleistungen & EDV
 * @package Vhs
 * @subpackage ViewHelpers
 */
class Tx_Vhs_ViewHelpers_Link_TypolinkViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Initializes the arguments for the ViewHelper
	 */
	public function initializeArguments() {
		$this->registerArgument('configuration', 'array', 'The typoLink configuration', TRUE);
	}

	/**
	 * @return mixed
	 */
	public function render() {
		return $GLOBALS['TSFE']->cObj->typoLink($this->renderChildren(), $this->arguments['configuration']);
	}
}
