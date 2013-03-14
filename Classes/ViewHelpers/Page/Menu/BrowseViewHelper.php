<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * ### Page: Browse Menu ViewHelper
 *
 * ViewHelper for rendering TYPO3 browse menus in Fluid
 *
 * Renders links to browse inside a menu branch including
 * first, previous, next, last and up to the parent page.
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
class Tx_Vhs_ViewHelpers_Page_Menu_BrowseViewHelper extends Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('labelFirst', 'string', 'Label for the "first" link', FALSE, 'first');
		$this->registerArgument('labelLast', 'string', 'Label for the "last" link', FALSE, 'last');
		$this->registerArgument('labelPrevious', 'string', 'Label for the "previous" link', FALSE, 'previous');
		$this->registerArgument('labelNext', 'string', 'Label for the "next" link', FALSE, 'next');
		$this->registerArgument('labelUp', 'string', 'Label for the "up" link', FALSE, 'up');
		$this->registerArgument('renderFirst', 'boolean', 'If set the "first" link will be rendered', FALSE, TRUE);
		$this->registerArgument('renderLast', 'boolean', 'If set the "last" link will be rendered', FALSE, TRUE);
		$this->registerArgument('renderUp', 'boolean', 'If set the "up" link will be rendered', FALSE, TRUE);
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$pageUid = $GLOBALS['TSFE']->id;
		$rootLineData = $this->pageSelect->getRootLine($pageUid);
		$currentPage = $this->pageSelect->getPage($pageUid);
		$parentUid = $currentPage['pid'];
		$parentPage = $this->pageSelect->getPage($parentUid);
		$menuData = $this->pageSelect->getMenu($parentUid);
		$pageUids = array_keys($menuData);
		$uidCount = count($pageUids);
		$firstUid = $pageUids[0];
		$lastUid = $pageUids[$uidCount - 1];
		for ($i = 0; $i < $uidCount; $i++) {
			if ($pageUids[$i] == $pageUid) {
				if ($i > 1) {
					$prevUid = $pageUids[$i - 1];
				}
				if ($i < $uidCount) {
					$nextUid = $pageUids[$i + 1];
				}
				break;
			}
		}
		$menuItems = array();
		if (TRUE === (boolean) $this->arguments['renderFirst']) {
			$menuItems[] = $menuData[$firstUid];
		}
		$menuItems[] = $menuData[$prevUid];
		if (TRUE === (boolean) $this->arguments['renderUp']) {
			$menuItems[] = $parentPage;
		}
		$menuItems[] = $menuData[$nextUid];
		if (TRUE === (boolean) $this->arguments['renderLast']) {
			$menuItems[] = $menuData[$lastUid];
		}
		$menu = $this->parseMenu($menuItems, $rootLineData);
		if (isset($menu[$firstUid])) {
			$menu[$firstUid]['linktext'] = $this->arguments['labelFirst'];
		}
		if (isset($menu[$prevUid])) {
			$menu[$prevUid]['linktext'] = $this->arguments['labelPrevious'];
		}
		if (isset($menu[$parentUid])) {
			$menu[$parentUid]['linktext'] = $this->arguments['labelUp'];
		}
		if (isset($menu[$nextUid])) {
			$menu[$nextUid]['linktext'] = $this->arguments['labelNext'];
		}
		if (isset($menu[$lastUid])) {
			$menu[$lastUid]['linktext'] = $this->arguments['labelLast'];
		}
		$rootLine = $this->parseMenu($rootLineData, $rootLineData);
		$this->backupVariables();
		$this->templateVariableContainer->add('menu', $menu);
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove('menu');
		$output = $this->renderContent($menu, $content);
		$this->restoreVariables();
		return $output;
	}

}
