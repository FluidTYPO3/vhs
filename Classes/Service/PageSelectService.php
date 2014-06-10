<?php
namespace FluidTYPO3\Vhs\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
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
 * Page Select Service
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
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PageSelectService implements SingletonInterface {

	/**
	 * @var \TYPO3\CMS\Frontend\Page\PageRepository
	 */
	protected static $pageSelect;

	/**
	 * @var array
	 */
	protected static $cachedPages = array();

	/**
	 * @var array
	 */
	protected static $cachedOverlays = array();

	/**
	 * @var array
	 */
	protected static $cachedMenus = array();

	/**
	 * @var array
	 */
	protected static $cachedRootLines = array();

	/**
	 * Initialize \TYPO3\CMS\Frontend\Page\PageRepository objects
	 */
	public function initializeObject() {
		self::$pageSelect = $this->createPageSelectInstance();
	}

	/**
	 * @return \TYPO3\CMS\Frontend\Page\PageRepository
	 */
	private function createPageSelectInstance() {
		if (TRUE === is_array($GLOBALS['TSFE']->fe_user->user)) {
			$groups = array(-2, 0);
			$groups = array_merge($groups, (array) array_values($GLOBALS['TSFE']->fe_user->groupData['uid']));
		} else {
			$groups = array(-1, 0);
		}
		$pageSelect = new \TYPO3\CMS\Frontend\Page\PageRepository();
		$clauses = array();
		foreach ($groups as $group) {
			$clause = "fe_group = '" . $group . "' OR fe_group LIKE '" .
				$group . ",%' OR fe_group LIKE '%," . $group . "' OR fe_group LIKE '%," . $group . ",%'";
			array_push($clauses, $clause);
		}
		array_push($clauses, "fe_group = '' OR fe_group = '0'");
		$pageSelect->where_groupAccess = ' AND (' . implode(' OR ', $clauses) .  ')';
		return $pageSelect;
	}

	/**
	 * Wrapper for \TYPO3\CMS\Frontend\Page\PageRepository::getPage()
	 *
	 * @param integer $pageUid
	 * @return array
	 */
	public function getPage($pageUid = NULL) {
		if (NULL === $pageUid) {
			$pageUid = $GLOBALS['TSFE']->id;
		}
		if (FALSE === isset(self::$cachedPages[$pageUid])) {
			self::$cachedPages[$pageUid] = self::$pageSelect->getPage($pageUid);
		}
		return self::$cachedPages[$pageUid];
	}

	/**
	 * Wrapper for \TYPO3\CMS\Frontend\Page\PageRepository::getPageOverlay()
	 *
	 * @param mixed $pageInput
	 * @param integer $languageUid
	 * @return array
	 */
	public function getPageOverlay($pageInput, $languageUid = -1) {
		$key = md5(serialize($pageInput) . $languageUid);
		if (FALSE === isset(self::$cachedOverlays[$key])) {
			self::$cachedOverlays[$key] = self::$pageSelect->getPageOverlay($pageInput, $languageUid);
		}
		return self::$cachedOverlays[$key];
	}

	/**
	 * Wrapper for \TYPO3\CMS\Frontend\Page\PageRepository::getMenu()
	 * Caution: different signature
	 *
	 * @param integer $pageUid
	 * @param array $excludePages
	 * @param string $where
	 * @param boolean $showHiddenInMenu
	 * @param boolean $checkShortcuts
	 * @param array $allowedDoktypeList
	 * @return array
	 */
	public function getMenu($pageUid = NULL, $excludePages = array(), $where = '', $showHiddenInMenu = FALSE, $checkShortcuts = FALSE, $allowedDoktypeList = array()) {
		if (NULL === $pageUid) {
			$pageUid = $GLOBALS['TSFE']->id;
		}
		$addWhere = self::$pageSelect->enableFields('pages');
		if (0 < count($allowedDoktypeList)) {
			$addWhere .= ' AND doktype IN (' . implode(',', $allowedDoktypeList) . ')';
		} else {
			$addWhere .= ' AND doktype != ' . \TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_SYSFOLDER;
		}
		if (0 < count($excludePages)) {
			$addWhere .= ' AND uid NOT IN (' . implode(',', $excludePages) . ')';
		}
		if (FALSE === (boolean) $showHiddenInMenu) {
			$addWhere .= ' AND nav_hide=0';
		}
		if ('' !== $where) {
			$addWhere = $where . ' ' . $addWhere;
		}
		$key = md5(intval($showHiddenInMenu) . $pageUid . $addWhere . intval($checkShortcuts));
		if (FALSE === isset(self::$cachedMenus[$key])) {
			self::$cachedMenus[$key] = self::$pageSelect->getMenu($pageUid, '*', 'sorting', $addWhere, $checkShortcuts);
		}
		return self::$cachedMenus[$key];
	}

	/**
	 * Wrapper for \TYPO3\CMS\Frontend\Page\PageRepository::getRootLine()
	 *
	 * @param integer $pageUid
	 * @param string $MP
	 * @param boolean $reverse
	 * @return array
	 */
	public function getRootLine($pageUid = NULL, $MP = NULL, $reverse = FALSE) {
		if (NULL === $pageUid) {
			$pageUid = $GLOBALS['TSFE']->id;
		}
		if (NULL === $MP) {
			$MP = GeneralUtility::_GP('MP');
			if (TRUE === empty($MP)) {
				$MP = '';
			}
		}
		$key = md5($pageUid . $MP);
		if (FALSE === isset(self::$cachedRootLines[$key])) {
			$rootLine = self::$pageSelect->getRootLine($pageUid, $MP);
			if (TRUE === $reverse) {
				$rootLine = array_reverse($rootLine);
			}
			self::$cachedRootLines[$key] = $rootLine;
		}
		return self::$cachedRootLines[$key];
	}

	/**
	 * Checks if a page for a specific language should be hidden
	 *
	 * @param integer $pageUid
	 * @param integer $languageUid
	 * @param boolean $normalWhenNoLanguage
	 * @return boolean
	 */
	public function hidePageForLanguageUid($pageUid = 0, $languageUid = -1, $normalWhenNoLanguage = TRUE) {
		if (0 === $pageUid) {
			$pageUid = $pageUid = $GLOBALS['TSFE']->id;
		}
		if (-1 === $languageUid) {
			$languageUid = $GLOBALS['TSFE']->sys_language_uid;
		}
		$page = $this->getPage($pageUid);
		$l18nCfg = TRUE === isset($page['l18n_cfg']) ? $page['l18n_cfg'] : 0;
		$hideIfNotTranslated = (boolean) GeneralUtility::hideIfNotTranslated($l18nCfg);
		$hideIfDefaultLanguage = (boolean) GeneralUtility::hideIfDefaultLanguage($l18nCfg);
		$pageOverlay = 0 !== $languageUid ? $this->getPageOverlay($pageUid, $languageUid) : array();
		$translationAvailable = 0 !== count($pageOverlay);
		return
			(TRUE === $hideIfNotTranslated && 0 !== $languageUid && FALSE === $translationAvailable) ||
			(TRUE === $hideIfDefaultLanguage && (0 === $languageUid || FALSE === $translationAvailable)) ||
			(FALSE === $normalWhenNoLanguage && 0 !== $languageUid && FALSE === $translationAvailable);
	}

}
