<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use TYPO3\CMS\Frontend\Page\PageRepository;

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
 * @subpackage ViewHelpers\Condition\Page
 */
class IsChildPageViewHelper extends AbstractConditionViewHelper {

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
		$pageSelect = new PageRepository();
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
