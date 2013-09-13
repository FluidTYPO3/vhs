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
 * Page Select Service
 *
 * Wrapper service for t3lib_pageSelect including static caches for
 * menus, rootlines, pages and page overlays to be implemented in
 * viewhelpers by replacing calls to t3lib_pageSelect::getMenu()
 * and the like.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage Service
 */
class Tx_Vhs_Service_PageSelectService implements t3lib_Singleton {

	/**
	 * @var t3lib_pageSelect
	 */
	private static $pageSelect;

	/**
	 * @var t3lib_pageSelect
	 */
	private static $pageSelectHidden;

	/**
	 * @var array
	 */
	private static $cachedPages = array();

	/**
	 * @var array
	 */
	private static $cachedOverlays = array();

	/**
	 * @var array
	 */
	private static $cachedMenus = array();

	/**
	 * @var array
	 */
	private static $cachedRootLines = array();

	/**
	 * Initialize t3lib_pageSelect objects
	 */
	public function initializeObject() {
		self::$pageSelect = $this->createPageSelectInstance(FALSE);
		self::$pageSelectHidden = $this->createPageSelectInstance(TRUE);
	}

	/**
	 * @param boolean $showHidden
	 * @return t3lib_pageSelect
	 */
	private function createPageSelectInstance($showHidden = FALSE) {
		if (TRUE === is_array($GLOBALS['TSFE']->fe_user->user)) {
			$groups = array(-2, 0);
			$groups = array_merge($groups, (array) array_values($GLOBALS['TSFE']->fe_user->groupData['uid']));
		} else {
			$groups = array(-1, 0);
		}
		$pageSelect = new t3lib_pageSelect();
		$pageSelect->init((boolean) $showHidden);
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
	 * Wrapper for t3lib_pageSelect::getPage()
	 *
	 * @param integer $pageUid
	 * @return array
	 */
	public function getPage($pageUid = NULL) {
		if (NULL === $pageUid) {
			$pageUid = $GLOBALS['TSFE']->id;
		}
		if (FALSE === isset(self::$cachedPages[$pageUid])) {
			$page = self::$pageSelect->getPage($pageUid);
			if (NULL!==$page['media']) {
				/* @var \TYPO3\CMS\Core\Resource\FileRepository $fileRepository */
				$fileRepository = \t3lib_div::makeInstance('TYPO3\CMS\Core\Resource\FileRepository');
				$fileReferences = $fileRepository->findByRelation('pages', 'media', $pageUid);
				if (!empty($fileReferences)) {
					$page['media'] = $fileReferences;
				}
			}
			self::$cachedPages[$pageUid] = $page;
		}
		return self::$cachedPages[$pageUid];
	}

	/**
	 * Wrapper for t3lib_pageSelect::getPageOverlay()
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
	 * Wrapper for t3lib_pageSelect::getMenu()
	 * Caution: different signature
	 *
	 * @param integer $pageUid
	 * @param boolean $showHidden
	 * @param array $excludePages
	 * @param string $where
	 * @param boolean $checkShortcuts
	 * @return array
	 */
	public function getMenu($pageUid = NULL, $showHidden = FALSE, $excludePages = array(), $where = '', $checkShortcuts = FALSE) {
		if (NULL === $pageUid) {
			$pageUid = $GLOBALS['TSFE']->id;
		}
		$addWhere = 0 < count($excludePages) ? 'AND uid NOT IN (' . implode(',', $excludePages) . ')' : '';
		if ('' !== $where) {
			$addWhere = $where . ' ' . $addWhere;
		}
		$key = md5(intval($showHidden) . $pageUid . $addWhere . intval($checkShortcuts));
		if (FALSE === isset(self::$cachedMenus[$key])) {
			if (TRUE === $showHidden) {
				self::$cachedMenus[$key] = self::$pageSelectHidden->getMenu($pageUid, '*', 'sorting', $addWhere, $checkShortcuts);
			} else {
				self::$cachedMenus[$key] = self::$pageSelect->getMenu($pageUid, '*', 'sorting', $addWhere, $checkShortcuts);
			}
		}
		return self::$cachedMenus[$key];
	}

	/**
	 * Wrapper for t3lib_pageSelect::getRootline()
	 *
	 * @param integer $pageUid
	 * @param string $MP
	 * @return array
	 */
	public function getRootline($pageUid = NULL, $MP = '') {
		if (NULL === $pageUid) {
			$pageUid = $GLOBALS['TSFE']->id;
		}
		$key = md5($pageUid . $MP);
		if (FALSE === isset(self::$cachedRootLines[$key])) {
			self::$cachedRootLines[$key] = self::$pageSelect->getRootLine($pageUid, $MP);
		}
		return self::$cachedRootLines[$key];
	}

}
