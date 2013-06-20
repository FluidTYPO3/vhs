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
 * ViewHelper to access data of the current page record
 *
 * @author Björn Fromme <fromeme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Page
 */
class Tx_Vhs_ViewHelpers_Page_InfoViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var Tx_Vhs_Service_PageSelectService
	 */
	protected $pageSelect;

	/**
	 * @param Tx_Vhs_Service_PageSelectService $pageSelect
	 * @return void
	 */
	public function injectPageSelectService(Tx_Vhs_Service_PageSelectService $pageSelect) {
		$this->pageSelect = $pageSelect;
	}

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('pageUid', 'integer', 'If specified, this UID will be used to fetch page data instead of using the current page.', FALSE, NULL);
		$this->registerArgument('as', 'string', 'If specified, a template variable with this name containing the requested data will be inserted instead of returning it.', FALSE, NULL);
		$this->registerArgument('field', 'string', 'If specified, only this field will be returned/assigned instead of the complete page record.', FALSE, NULL);
	}

	/**
	 * @return mixed
	 */
	public function render() {
		$pageUid = NULL !== $this->arguments['pageUid'] ? intval($this->arguments['pageUid']) : $GLOBALS['TSFE']->id;
		$page = $this->pageSelect->getPage($pageUid);
		$output = NULL;
		if (TRUE === empty($this->arguments['field'])) {
			$output = $page;
		} else {
			$field = $this->arguments['field'];
			if (TRUE === isset($page[$field])) {
				$output = $page[$field];
			}
		}
		if (FALSE === empty($this->arguments['as'])) {
			if ($this->templateVariableContainer->exists($this->arguments['as'])) {
				$this->templateVariableContainer->remove($this->arguments['as']);
			}
			$this->templateVariableContainer->add($this->arguments['as'], $output);
			return NULL;
		} else {
			return $output;
		}
	}

}
