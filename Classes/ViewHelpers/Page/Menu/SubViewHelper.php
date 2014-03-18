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
 * ### Page: Auto Sub Menu ViewHelper
 *
 * Recycles the parent menu ViewHelper instance, resetting the
 * page UID used as starting point and repeating rendering of
 * the exact same tag content.
 *
 * Used in custom menu rendering to indicate where a submenu is
 * to be rendered; accepts only a single argument called `pageUid`
 * which defines the new starting page UID that is used in the
 * recycled parent menu instance.
 *
 * @author Björn Fromme <fromeme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Page
 */
class Tx_Vhs_ViewHelpers_Page_Menu_SubViewHelper extends Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('pageUid', 'mixed', 'Page UID to be overridden in the recycled rendering of the parent instance, if one exists', TRUE);
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$pageUid = $this->arguments['pageUid'];
		$parentInstance = $this->retrieveReconfiguredParentMenuInstance($pageUid);
		if (NULL === $parentInstance) {
			return '';
		}
		$parentArguments = $parentInstance->getArguments();
		$currentPageRootLine = $this->pageSelect->getRootLine();
		$isActive = $this->isActive($pageUid, $currentPageRootLine);
		// Note about next case: although $isCurrent in most cases implies $isActive, cases where the menu item
		// that is being rendered is in fact the current page but is NOT part of the rootline of the menu being
		// rendered - which is expected for example if using a page setting to render a different page in menus.
		// This means that the following check although it appears redundant, it is in fact not.
		$isCurrent = $this->isCurrent($pageUid);
		$isExpanded = (TRUE === isset($parentArguments['expandAll']) && 0 < $parentArguments['expandAll']);
		$shouldRender = (TRUE === $isActive || TRUE === $isCurrent || TRUE === $isExpanded);
		if (FALSE === $shouldRender) {
			return '';
		}
		// retrieve the set of template variables which were in play when the parent menu VH started rendering.
		$variables = $this->viewHelperVariableContainer->get('Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper', 'variables');
		$parentInstance->setOriginal(FALSE);
		$content = $parentInstance->render();
		// restore the previous set of variables after they most likely have changed during the render() above.
		foreach ($variables as $name => $value) {
			if ($this->templateVariableContainer->exists($name)) {
				$this->templateVariableContainer->remove($name);
				$this->templateVariableContainer->add($name, $value);
			}
		}
		return $content;
	}

}
