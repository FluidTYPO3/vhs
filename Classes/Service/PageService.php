<?php

namespace FluidTYPO3\Vhs\Service;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Page Service
 *
 * Wrapper service for \TYPO3\CMS\Frontend\Page\PageRepository including static caches for
 * menus, rootlines, pages and page overlays to be implemented in
 * viewhelpers by replacing calls to \TYPO3\CMS\Frontend\Page\PageRepository::getMenu()
 * and the like.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage Service
 */
class PageService implements SingletonInterface {

	const DOKTYPE_MOVE_TO_PLACEHOLDER = 0;

	/**
	 * @var array
	 */
	protected static $cachedPages = array();

	/**
	 * @var array
	 */
	protected static $cachedMenus = array();

	/**
	 * @var array
	 */
	protected static $cachedRootlines = array();

	/**
	 * @param integer $pageUid
	 * @param array $excludePages
	 * @param boolean $includeNotInMenu
	 * @param boolean $includeMenuSeparator
	 * @param boolean $disableGroupAccessCheck
	 *
	 * @return array
	 */
	public function getMenu($pageUid, array $excludePages = array(), $includeNotInMenu = FALSE, $includeMenuSeparator = FALSE, $disableGroupAccessCheck = FALSE) {
		$pageRepository = $this->getPageRepository();
		$pageConstraints = $this->getPageConstraints($excludePages, $includeNotInMenu, $includeMenuSeparator);
		$cacheKey = md5($pageUid . $pageConstraints . (integer) $disableGroupAccessCheck);
		if (FALSE === isset(self::$cachedMenus[$cacheKey])) {
			if (TRUE === (boolean) $disableGroupAccessCheck) {
				$pageRepository->where_groupAccess = '';
			}
			self::$cachedMenus[$cacheKey] = $pageRepository->getMenu(
				$pageUid,
				'*',
				'sorting',
				$pageConstraints
			);
		}

		return self::$cachedMenus[$cacheKey];
	}

	/**
	 * @param integer $pageUid
	 * @param boolean $disableGroupAccessCheck
	 * @return array
	 */
	public function getPage($pageUid, $disableGroupAccessCheck = FALSE) {
		$cacheKey = md5($pageUid . (integer) $disableGroupAccessCheck);
		if (FALSE === isset(self::$cachedPages[$cacheKey])) {
			self::$cachedPages[$cacheKey] = $this->getPageRepository()->getPage($pageUid, $disableGroupAccessCheck);
		}

		return self::$cachedPages[$cacheKey];
	}

	/**
	 * @param integer $pageUid
	 * @param boolean $reverse
	 * @param boolean $disableGroupAccessCheck
	 * @return array
	 */
	public function getRootLine($pageUid = NULL, $reverse = FALSE, $disableGroupAccessCheck = FALSE) {
		if (NULL === $pageUid) {
			$pageUid = $GLOBALS['TSFE']->id;
		}
		$cacheKey = md5($pageUid . (integer) $reverse . (integer) $disableGroupAccessCheck);
		if (FALSE === isset(self::$cachedRootlines[$cacheKey])) {
			$pageRepository = $this->getPageRepository();
			if (TRUE === (boolean) $disableGroupAccessCheck) {
				$pageRepository->where_groupAccess = '';
			}
			$rootline = $pageRepository->getRootLine($pageUid);
			if (TRUE === $reverse) {
				$rootline = array_reverse($rootline);
			}
			self::$cachedRootlines[$cacheKey] = $rootline;
		}

		return self::$cachedRootlines[$cacheKey];
	}

	/**
	 * @param array $excludePages
	 * @param boolean $includeNotInMenu
	 * @param boolean $includeMenuSeparator
	 *
	 * @return string
	 */
	protected function getPageConstraints(array $excludePages = array(), $includeNotInMenu = FALSE, $includeMenuSeparator = FALSE) {
		$constraints = array();

		$constraints[] = 'doktype NOT IN (' . PageRepository::DOKTYPE_BE_USER_SECTION . ',' . PageRepository::DOKTYPE_RECYCLER . ',' . PageRepository::DOKTYPE_SYSFOLDER . ')';

		if ($includeNotInMenu === FALSE) {
			$constraints[] = 'nav_hide = 0';
		}

		if ($includeMenuSeparator === FALSE) {
			$constraints[] = 'doktype != ' . PageRepository::DOKTYPE_SPACER;
		}

		if (0 < count($excludePages)) {
			$constraints[] = 'uid NOT IN (' . implode(',', $excludePages) . ')';
		}

		return 'AND ' . implode(' AND ', $constraints);
	}

	/**
	 * @param array|integer $page
	 * @param integer $languageUid
	 * @param boolean $normalWhenNoLanguage
	 * @return boolean
	 */
	public function hidePageForLanguageUid($page = NULL, $languageUid = -1, $normalWhenNoLanguage = TRUE) {
		if (is_array($page)) {
			$pageUid = $page['uid'];
			$pageRecord = $page;
		} else {
			$pageUid = (0 === (integer) $page) ? $GLOBALS['TSFE']->id : (integer) $page;
			$pageRecord = $this->getPage($pageUid);
		}
		if (-1 === (integer) $languageUid) {
			$languageUid = $GLOBALS['TSFE']->sys_language_uid;
		}
		$l18nCfg = TRUE === isset($pageRecord['l18n_cfg']) ? $pageRecord['l18n_cfg'] : 0;
		$hideIfNotTranslated = (boolean) GeneralUtility::hideIfNotTranslated($l18nCfg);
		$hideIfDefaultLanguage = (boolean) GeneralUtility::hideIfDefaultLanguage($l18nCfg);
		$pageOverlay = (0 !== $languageUid) ? $GLOBALS['TSFE']->sys_page->getPageOverlay($pageUid, $languageUid) : array();
		$translationAvailable = (0 !== count($pageOverlay));

		return
			(TRUE === $hideIfNotTranslated && (0 !== $languageUid) && FALSE === $translationAvailable) ||
			(TRUE === $hideIfDefaultLanguage && ((0 === $languageUid) || FALSE === $translationAvailable)) ||
			(FALSE === $normalWhenNoLanguage && (0 !== $languageUid) && FALSE === $translationAvailable);
	}

	/**
	 * @return PageRepository
	 */
	protected function getPageRepository() {
		return clone $GLOBALS['TSFE']->sys_page;
	}

}
