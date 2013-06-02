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
 * ### Condition: Page is child page
 *
 * Condition ViewHelper which renders the `then` child if current
 * page or page with provided UID is a child of some other page in
 * the page tree. If $respectSiteRoot is set to TRUE root pages are
 * never considered child pages even if they are.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\If\Page
 */
class Tx_Vhs_ViewHelpers_If_Page_IsChildPageViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractConditionViewHelper {

	/**
	 * Render method
	 *
	 * @param integer $pageUid
	 * @param boolean $respectSiteRoot
	 * @return string
	 */
	public function render($pageUid = NULL, $respectSiteRoot = FALSE) {
		if (NULL === $pageUid || TRUE === empty($pageUid) || 0 === intval($pageUid)) {
			$pageUid = $GLOBALS['TSFE']->id;
		}
		$pageSelect = new t3lib_pageSelect();
		$page = $pageSelect->getPage($pageUid);
		if (TRUE === (boolean) $respectSiteRoot && TRUE === isset($page['is_siteroot']) && TRUE === (boolean) $page['is_siteroot']) {
			return $this->renderElseChild();
		}
		if (TRUE === isset($page['pid']) && 0 < $page['pid']) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}
}
