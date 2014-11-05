<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Menu;

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
class BrowseViewHelper extends AbstractMenuViewHelper {

	/**
	 * @var array
	 */
	protected $backups = array('menu');

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
		$this->registerArgument('renderFirst', 'boolean', 'If set to FALSE the "first" link will not be rendered', FALSE, TRUE);
		$this->registerArgument('renderLast', 'boolean', 'If set to FALSE the "last" link will not be rendered', FALSE, TRUE);
		$this->registerArgument('renderUp', 'boolean', 'If set to FALSE the "up" link will not be rendered', FALSE, TRUE);
		$this->registerArgument('usePageTitles', 'boolean', 'If set to TRUE, uses target page titles instead of "next", "previous" etc. labels', FALSE, FALSE);
		$this->registerArgument('pageUid', 'integer', 'Optional parent page UID to use as top level of menu. If unspecified, current page UID is used', FALSE, NULL);
		$this->registerArgument('currentPageUid', 'integer', 'Optional page UID to use as current page. If unspecified, current page UID from globals is used', FALSE, NULL);
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$pageUid = (integer) (NULL !== $this->arguments['pageUid'] ? $this->arguments['pageUid'] : $GLOBALS['TSFE']->id);
		$currentUid = (integer) (NULL !== $this->arguments['currentPageUid'] ? $this->arguments['currentPageUid'] : $GLOBALS['TSFE']->id);
		$currentPage = $this->pageSelect->getPage($currentUid);
		$rootLineData = $this->pageSelect->getRootLine($pageUid);
		$parentUid = (integer) (NULL !== $this->arguments['pageUid'] ? $pageUid : $currentPage['pid']);
		$parentPage = $this->pageSelect->getPage($parentUid);
		$menuData = $this->getMenu($parentUid);
		$pageUids = array_keys($menuData);
		$uidCount = count($pageUids);
		$firstUid = $pageUids[0];
		$lastUid = $pageUids[$uidCount - 1];
		$nextUid = NULL;
		$prevUid = NULL;
		for ($i = 0; $i < $uidCount; $i++) {
			if ((integer)$pageUids[$i] === $currentUid) {
				if ($i > 0) {
					$prevUid = $pageUids[$i - 1];
				}
				if ($i < $uidCount) {
					$nextUid = $pageUids[$i + 1];
				}
				break;
			}
		}
		$pages = array();
		if (TRUE === (boolean)$this->arguments['renderFirst']) {
			$pages['first'] = $menuData[$firstUid];
		}
		if (NULL !== $prevUid) {
			$pages['prev'] = $menuData[$prevUid];
		}
		if (TRUE === (boolean)$this->arguments['renderUp']) {
			$pages['up'] = $parentPage;
		}
		if (NULL !== $nextUid) {
			$pages['next'] = $menuData[$nextUid];
		}
		if (TRUE === (boolean)$this->arguments['renderLast']) {
			$pages['last'] = $menuData[$lastUid];
		}
		$menuItems = $this->parseMenu($pages, $rootLineData);
		$menu = array();
		if (TRUE === isset($pages['first'])) {
			$menu['first'] = $menuItems['first'];
			$menu['first']['linktext'] = $this->getCustomLabelOrPageTitle('labelFirst', $menuItems['first']);
		}
		if (TRUE === isset($pages['prev'])) {
			$menu['prev'] = $menuItems['prev'];
			$menu['prev']['linktext'] = $this->getCustomLabelOrPageTitle('labelPrevious', $menuItems['prev']);
		}
		if (TRUE === isset($pages['up'])) {
			$menu['up'] = $menuItems['up'];
			$menu['up']['linktext'] = $this->getCustomLabelOrPageTitle('labelUp', $menuItems['up']);
		}
		if (TRUE === isset($pages['next'])) {
			$menu['next'] = $menuItems['next'];
			$menu['next']['linktext'] = $this->getCustomLabelOrPageTitle('labelNext', $menuItems['next']);
		}
		if (TRUE === isset($pages['last'])) {
			$menu['last'] = $menuItems['last'];
			$menu['last']['linktext'] = $this->getCustomLabelOrPageTitle('labelLast', $menuItems['last']);
		}
		$this->backupVariables();
		$this->templateVariableContainer->add($this->arguments['as'], $menu);
		$output = $this->renderContent($menu);
		$this->templateVariableContainer->remove($this->arguments['as']);
		$this->restoreVariables();
		return $output;
	}


	/**
	 * @todo Consider moving this, and argument usePageTitles, to AbstractMenuViewHelper; however, this is not merited by the current Menu VH collection
	 * @param string $labelName
	 * @param array $pageRecord
	 * @return string
	 */
	protected function getCustomLabelOrPageTitle($labelName, $pageRecord) {
		$title = $this->arguments[$labelName];
		if (TRUE === $this->arguments['usePageTitles']) {
			$title = $this->getItemTitle($pageRecord);
		}
		return $title;
	}

}
