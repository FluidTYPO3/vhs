<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Menu;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

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
class DirectoryViewHelper extends AbstractMenuViewHelper {

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
			return '';
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
