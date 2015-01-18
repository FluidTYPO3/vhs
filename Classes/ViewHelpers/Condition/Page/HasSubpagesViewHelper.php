<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageSelectService;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Condition: Page has subpages
 *
 * A condition ViewHelper which renders the `then` child if
 * current page or page with provided UID has subpages. By default
 * disabled subpages are considered non existent which can be overridden
 * by setting $includeHidden to TRUE. To include pages that are hidden
 * in menus set $showHiddenInMenu to TRUE.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Condition\Page
 */
class HasSubpagesViewHelper extends AbstractConditionViewHelper {

	/**
	 * @var PageSelectService
	 */
	protected $pageSelect;

	/**
	 * @param PageSelectService $pageSelect
	 * @return void
	 */
	public function injectPageSelectService(PageSelectService $pageSelect) {
		$this->pageSelect = $pageSelect;
	}

	/**
	 * Render method
	 *
	 * @param integer $pageUid
	 * @param boolean $includeHidden
	 * @param boolean $showHiddenInMenu
	 * @return string
	 */
	public function render($pageUid = NULL, $includeHidden = FALSE, $showHiddenInMenu = FALSE) {
		if (NULL === $pageUid || TRUE === empty($pageUid) || 0 === intval($pageUid)) {
			$pageUid = $GLOBALS['TSFE']->id;
		}
		$menu = $this->pageSelect->getMenu($pageUid, array(), '', $showHiddenInMenu);
		$pageHasSubPages = (0 < count($menu));
		if (TRUE === $pageHasSubPages) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}

}
