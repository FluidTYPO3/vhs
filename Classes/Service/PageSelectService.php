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
 * Wrapper for t3lib_pageSelect including a static cache
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
	 * @var bool
	 */
	private static $showHidden = FALSE;

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
	 * @var Tx_Vhs_Service_PageSelectService
	 */
	private static $instance;

	/**
	 * Initialize t3lib_pageSelect object
	 */
	private function __construct() {
		self::$pageSelect = $this->createPageSelectInstance(FALSE);
		self::$pageSelectHidden = $this->createPageSelectInstance(TRUE);
	}

	/**
	 * Avoid cloning
	 */
	private function __clone() {}

	/**
	 * @param boolean $showHidden
	 * @return t3lib_pageSelect
	 */
	private function createPageSelectInstance($showHidden = FALSE) {
		if (is_array($GLOBALS['TSFE']->fe_user->user) === TRUE) {
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
	 * @param boolean $showHidden
	 */
	public function setShowHidden($showHidden = FALSE) {
		self::$showHidden = (boolean) $showHidden;
	}

	/**
	 * @return Tx_Vhs_Service_PageSelectService
	 */
	public static function getInstance() {
		if (NULL === self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
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
	 * @param mixed $pageInput
	 * @param integer $lUid
	 * @return array
	 */
	public function getPageOverlay($pageInput, $lUid = -1) {
		$key = md5(json_encode(array($pageInput, $lUid)));
		if (FALSE === isset(self::$cachedOverlays[$key])) {
			self::$cachedOverlays[$key] = self::$pageSelect->getMenu($pageUid, $fields, $sortField, $addWhere, $checkShortcuts);
		}
		return self::$cachedOverlays[$key];
	}

	/**
	 * @param integer $pageUid
	 * @param string $fields
	 * @param string $sortField
	 * @param string $addWhere
	 * @param int $checkShortcuts
	 * @return array
	 */
	public function getMenu($pageUid = NULL, $fields = '*', $sortField = 'sorting', $addWhere = '', $checkShortcuts = 1) {
		if (NULL === $pageUid) {
			$pageUid = $GLOBALS['TSFE']->id;
		}
		$key = md5(json_encode(array(self::$showHidden, $pageUid, $fields, $sortField, $addWhere, $checkShortcuts)));
		if (FALSE === isset(self::$cachedMenus[$key])) {
			if (TRUE === self::$showHidden) {
				self::$cachedMenus[$key] = self::$pageSelectHidden->getMenu($pageUid, $fields, $sortField, $addWhere, $checkShortcuts);
			} else {
				self::$cachedMenus[$key] = self::$pageSelect->getMenu($pageUid, $fields, $sortField, $addWhere, $checkShortcuts);
			}
		}
		return self::$cachedMenus[$key];
	}

	/**
	 * @param integer $pageUid
	 * @return array
	 */
	public function getRootline($pageUid = NULL) {
		if (NULL === $pageUid) {
			$pageUid = $GLOBALS['TSFE']->id;
		}
		$key = md5(json_encode(array(self::$showHidden, $pageUid)));
		if (FALSE === isset(self::$cachedRootLines[$key])) {
			if (TRUE === self::$showHidden) {
				self::$cachedRootLines[$key] = self::$pageSelectHidden->getRootLine($pageUid);
			} else {
				self::$cachedRootLines[$key] = self::$pageSelect->getRootLine($pageUid);
			}
		}
		return self::$cachedRootLines[$key];
	}

}
