<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Menu\AbstractMenuViewHelper;

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
		$pageUid = $this->arguments['pageUid'] > 0 ? $this->arguments['pageUid'] : $GLOBALS['TSFE']->id;
		$entryLevel = $this->arguments['entryLevel'];
		$endLevel = $this->arguments['endLevel'];
		$rawRootLineData = $this->pageService->getRootLine($pageUid);
		$rawRootLineData = array_reverse($rawRootLineData);
		$rawRootLineData = array_slice($rawRootLineData, $entryLevel, $endLevel);
		$rootLineData = $rawRootLineData;
		if (FALSE === (boolean) $this->arguments['showHiddenInMenu']) {
			$rootLineData = array();
			foreach ($rawRootLineData as $record) {
				if (FALSE === (boolean) $record['nav_hide']) {
					array_push($rootLineData, $record);
				}
			}
		}
		$rootLine = $this->parseMenu($rootLineData);
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
