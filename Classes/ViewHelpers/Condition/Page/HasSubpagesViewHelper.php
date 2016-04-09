<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
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
	 * @var PageService
	 */
	static protected $pageService;

	/**
	 * @param PageService $pageService
	 * @return void
	 */
	static public function setPageService(PageService $pageService) {
		self::$pageService = $pageService;
	}

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('pageUid', 'integer', 'Parent page to check', FALSE, NULL);
		$this->registerArgument('includeHidden', 'boolean', 'DEPRECATED: Include hidden pages', FALSE, FALSE);
		$this->registerArgument('includeAccessProtected', 'boolean', 'Include access protected pages', FALSE, FALSE);
		$this->registerArgument('includeHiddenInMenu', 'boolean', 'Include pages hidden in menu', FALSE, FALSE);
		$this->registerArgument('showHiddenInMenu', 'boolean', 'DEPRECATED: Use argument includeHiddenInMenu', FALSE, FALSE);
	}

	/**
	 * @param array $arguments
	 * @return boolean
	 */
	static protected function evaluateCondition($arguments = NULL) {
		$pageUid = $arguments['pageUid'];
		$includeHiddenInMenu = (boolean) $arguments['includeHiddenInMenu'];
		$includeAccessProtected = (boolean) $arguments['includeAccessProtected'];

		if (NULL === $pageUid || TRUE === empty($pageUid) || 0 === (integer) $pageUid) {
			$pageUid = $GLOBALS['TSFE']->id;
		}

		if (self::$pageService === NULL) {
			$objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
			self::$pageService = $objectManager->get('FluidTYPO3\Vhs\Service\PageService');
		}

		$menu = self::$pageService->getMenu($pageUid, array(), $includeHiddenInMenu, FALSE, $includeAccessProtected);

		return (0 < count($menu));
	}

}
