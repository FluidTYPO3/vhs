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
 * ViewHelper to make a breadcrumb link set from a pageUid, automatic or manual
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers\Page
 */
class Tx_Vhs_ViewHelpers_Page_BreadCrumbViewHelper extends Tx_Vhs_ViewHelpers_Page_AbstractMenuViewHelper {

	/**
	 * @return string
	 */
	public function render() {
		$pageUid = $this->arguments['pageUid'] > 0 ? $this->arguments['pageUid'] : $GLOBALS['TSFE']->id;
		$rootLine = $this->pageSelect->getRootLine($pageUid);
		$rootLine = array_reverse($rootLine);
		$rootLine = array_slice($rootLine, $this->arguments['entryLevel']);
		$rootLine = $this->parseMenu($rootLine, $rootLine);
		if (count($rootLine) === 0) {
			return NULL;
		}
		$backupVars = $this->arguments['backupVariables'];
		$backups = array();
		foreach ($backupVars as $var) {
			if ($this->templateVariableContainer->exists($var)) {
				$backups[$var] = $this->templateVariableContainer->get($var);
				$this->templateVariableContainer->remove($var);
			}
		}
		$this->templateVariableContainer->add('rootLine', $rootLine);
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove('rootLine');
		if (strlen(trim($content)) === 0) {
			$content = $this->autoRender($rootLine);
			if (strlen(trim($content)) === 0) {
				$content = '';
			} else {
				$this->tag->setTagName($this->arguments['tagName']);
				$this->tag->setContent($content);
				$this->tag->forceClosingTag(TRUE);
				$content = $this->tag->render();
			}
		}
		if (count($backups) > 0) {
			foreach ($backups as $var => $value) {
				$this->templateVariableContainer->add($var, $value);
			}
		}
		return $content;
	}

}
