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
 * ### Page: Directory Menu ViewHelper
 *
 * ViewHelper for rendering TYPO3 list menus in Fluid
 *
 * Supports both automatic, tag-based rendering (which
 * defaults to `ul > li` with options to set both the
 * parent and child tag names. When using manual rendering
 * a range of support CSS classes are available along
 * with each page record.
 *
 * @author Björn Fromme <fromeme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Page
 */
class Tx_Vhs_ViewHelpers_Page_Menu_DirectoryViewHelper extends Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper {

	/**
	 * @var array
	 */
	protected $backups = array('menu');

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('pages', 'mixed', 'Parent page UIDs of subpages to include in the menu. Can be CSV, array or an object implementing Traversable.', TRUE);
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$pages = $this->processPagesArgument();
		if (0 === count($pages)) {
			return;
		}
		$menuData = array();
		$rootLineData = $this->pageSelect->getRootLine();
		foreach ($pages as $pageUid) {
			$menuData = array_merge($menuData, $this->getMenu($pageUid));
		}
		$menu = $this->parseMenu($menuData, $rootLineData);
		$this->backupVariables();
		$this->templateVariableContainer->add($this->arguments['as'], $menu);
		$output = $this->renderContent($menu);
		$this->templateVariableContainer->remove($this->arguments['as']);
		$this->restoreVariables();
		return $output;
	}

}
