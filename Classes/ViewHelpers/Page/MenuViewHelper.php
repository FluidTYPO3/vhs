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
 * ### Page: Menu ViewHelper
 *
 * ViewHelper for rendering TYPO3 menus in Fluid
 *
 * Supports both automatic, tag-based rendering (which
 * defaults to `ul > li` with options to set both the
 * parent and child tag names. When using manual rendering
 * a range of support CSS classes are available along
 * with each page record.
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers\Page
 */
class Tx_Vhs_ViewHelpers_Page_MenuViewHelper extends Tx_Vhs_ViewHelpers_Page_AbstractMenuViewHelper {

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$pageUid = $this->arguments['pageUid'];
		$entryLevel = $this->arguments['entryLevel'];
		$rootLine = $this->pageSelect->getRootLine($GLOBALS['TSFE']->id);
		if (!$pageUid) {
			if ($rootLine[$entryLevel]['uid'] !== NULL) {
				$pageUid = $rootLine[$entryLevel]['uid'];
			} else {
				return '';
			}
		}
		$menu = $this->pageSelect->getMenu($pageUid);
		$menu = $this->parseMenu($menu, $rootLine);
		$rootLine = $this->parseMenu($rootLine, $rootLine);
		$backupVars = $this->arguments['backupVariables'];
		$backups = array();
		foreach ($backupVars as $var) {
			if ($this->templateVariableContainer->exists($var)) {
				$backups[$var] = $this->templateVariableContainer->get($var);
				$this->templateVariableContainer->remove($var);
			}
		}
		$this->templateVariableContainer->add('menu', $menu);
		$this->templateVariableContainer->add('rootLine', $rootLine);
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove('menu');
		$this->templateVariableContainer->remove('rootLine');
		if (strlen(trim($content)) === 0) {
			$content = $this->autoRender($menu);
			if (strlen(trim($content)) === 0) {
				$output = '';
			} else {
				$this->tag->setTagName($this->arguments['tagName']);
				$this->tag->setContent($content);
				$this->tag->forceClosingTag(TRUE);
				$output = $this->tag->render();
			}
		} else {
			$output = $content;
		}
		if (count($backups) > 0) {
			foreach ($backups as $var => $value) {
				$this->templateVariableContainer->add($var, $value);
			}
		}
		return $output;
	}

}
