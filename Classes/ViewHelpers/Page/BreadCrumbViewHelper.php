<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

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
use FluidTYPO3\Vhs\ViewHelpers\Page\Menu\AbstractMenuViewHelper;

/**
 * ViewHelper to make a breadcrumb link set from a pageUid, automatic or manual
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @author Björn Fromme <fromeme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Page
 */
class BreadCrumbViewHelper extends AbstractMenuViewHelper {

	/**
	 * @var array
	 */
	protected $backups = array('rootLine');

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('pageUid', 'integer', 'Optional parent page UID to use as top level of menu. If left out will be detected from rootLine using $entryLevel.', FALSE, NULL);
		$this->registerArgument('endLevel', 'integer', 'Optional deepest level of rendering. If left out all levels up to the current are rendered.', FALSE, NULL);
		$this->overrideArgument('as', 'string', 'If used, stores the menu pages as an array in a variable named after this value and renders the tag content. If the tag content is empty automatic rendering is triggered.', FALSE, 'breadcrumb');
	}

	/**
	 * @return string
	 */
	public function render() {
		$this->backups = array($this->arguments['as']);
		$pageUid = $this->arguments['pageUid'] > 0 ? $this->arguments['pageUid'] : $GLOBALS['TSFE']->id;
		$entryLevel = $this->arguments['entryLevel'];
		$endLevel = $this->arguments['endLevel'];
		$rootLineData = $this->pageSelect->getRootLine($pageUid);
		$rootLineData = array_reverse($rootLineData);
		$rootLineData = array_slice($rootLineData, $entryLevel, $endLevel);
		$rootLine = $this->parseMenu($rootLineData, $rootLineData);
		if (0 === count($rootLine)) {
			return NULL;
		}
		$this->backupVariables();
		$this->templateVariableContainer->add($this->arguments['as'], $rootLine);
		$output = $this->renderContent($rootLine);
		$this->templateVariableContainer->remove($this->arguments['as']);
		$this->restoreVariables();
		return $output;
	}

}
