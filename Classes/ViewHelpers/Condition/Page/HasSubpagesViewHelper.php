<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageSelectService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use FluidTYPO3\Vhs\Traits\ConditionViewHelperTrait;

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

	use ConditionViewHelperTrait;

	/**
	 * @var PageSelectService
	 */
	static protected $pageSelect;

	/**
	 * @param PageSelectService $pageSelect
	 * @return void
	 */
	static public function setPageSelectService(PageSelectService $pageSelect) {
		self::$pageSelect = $pageSelect;
	}

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('pageUid', 'integer', 'value to check', FALSE, NULL);
		$this->registerArgument('includeHidden', 'boolean', 'include hidden pages', FALSE, FALSE);
		$this->registerArgument('showHiddenInMenu', 'boolean', 'include pages hidden in menu', FALSE, FALSE);
	}

	/**
	 * This method decides if the condition is TRUE or FALSE. It can be overriden in extending viewhelpers to adjust functionality.
	 *
	 * @param array $arguments ViewHelper arguments to evaluate the condition for this ViewHelper, allows for flexiblity in overriding this method.
	 * @return bool
	 */
	static protected function evaluateCondition($arguments = NULL) {
		$pageUid = $arguments['pageUid'];
		$includeHidden = $arguments['includeHidden'];
		$showHiddenInMenu = $arguments['showHiddenInMenu'];

		if (NULL === $pageUid || TRUE === empty($pageUid) || 0 === intval($pageUid)) {
			$pageUid = $GLOBALS['TSFE']->id;
		}

		if (self::$pageSelect === NULL) {
			$objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
			self::$pageSelect = $objectManager->get('FluidTYPO3\Vhs\Service\PageSelectService');
		}

		$menu = self::$pageSelect->getMenu($pageUid, array(), '', $showHiddenInMenu);
		$pageHasSubPages = (0 < count($menu));
		return TRUE === $pageHasSubPages;
	}

}
