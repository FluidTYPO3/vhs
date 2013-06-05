<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
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
 ***************************************************************/

/**
 * ### Condition: Page has subpages
 *
 * A condition ViewHelper which renders the `then` child if
 * current page or page with provided UID has subpages. By default
 * hidden subpages are considered non existent which can be overridden
 * by setting $includeHidden to TRUE.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\If\Page
 */
class Tx_Vhs_ViewHelpers_If_Page_HasSubpagesViewHelper extends Tx_Vhs_ViewHelpers_Condition_AbstractClientInformationViewHelper {

	/**
	 * Render method
	 *
	 * @param integer $pageUid
	 * @param boolean $includeHidden
	 * @return string
	 */
	public function render($pageUid = NULL, $includeHidden = FALSE) {
		if (NULL === $pageUid || TRUE === empty($pageUid) || 0 === intval($pageUid)) {
			$pageUid = $GLOBALS['TSFE']->id;
		}
		$pageSelect = new t3lib_pageSelect();
		$pageSelect->init((boolean) $includeHidden);
		$pageHasSubPages = (0 < count($pageSelect->getMenu($pageUid))) ? 1 : 0;
		if (1 === $pageHasSubPages) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}
}
