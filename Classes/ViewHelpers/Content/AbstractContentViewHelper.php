<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * ************************************************************* */

/**
 * ### Base class: Content ViewHelpers
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @author Dominique Feyer, <dfeyer@ttree.ch>
 * @author Daniel Schöne, <daniel@schoene.it>
 * @author Björn Fromme, <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Content
 */
abstract class Tx_Vhs_ViewHelpers_Content_AbstractContentViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var tslib_cObj
	 */
	protected $contentObject;

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @var Tx_Vhs_Service_PageSelectService
	 */
	protected $pageSelect;

	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
		$this->contentObject = $this->configurationManager->getContentObject();
	}

	/**
	 * @param Tx_Vhs_Service_PageSelectService $pageSelect
	 * @return void
	 */
	public function injectPageSelectService(Tx_Vhs_Service_PageSelectService $pageSelect) {
		$this->pageSelect = $pageSelect;
	}

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		$this->registerArgument('column', 'integer', 'Name of the column to render', FALSE, 0);
		$this->registerArgument('limit', 'integer', 'Optional limit to the number of content elements to render');
		$this->registerArgument('order', 'string', 'Optional sort field of content elements - RAND() supported', FALSE, 'sorting');
		$this->registerArgument('sortDirection', 'string', 'Optional sort direction of content elements', FALSE, 'ASC');
		$this->registerArgument('pageUid', 'integer', 'If set, selects only content from this page UID');
		$this->registerArgument('contentUids', 'array', 'If used, replaces all conditions with an "uid IN (1,2,3)" style condition using the UID values from this array');
		$this->registerArgument('slide', 'integer', 'Enables Content Sliding - amount of levels which shall get walked up the rootline. For infinite sliding (till the rootpage) set to -1)', FALSE, 0);
		$this->registerArgument('slideCollect', 'integer', 'Enables collecting of Content Elements - amount of levels which shall get walked up the rootline. For infinite sliding (till the rootpage) set to -1 (lesser value for slide and slide.collect applies))', FALSE, 0);
		$this->registerArgument('slideCollectReverse', 'boolean', 'Normally when collecting content elements the elements from the actual page get shown on the top and those from the parent pages below those. You can invert this behaviour (actual page elements at bottom) by setting this flag))', FALSE, 0);
		$this->registerArgument('loadRegister', 'array', 'List of LOAD_REGISTER variable');
		$this->registerArgument('render', 'boolean', 'Optional returning variable as original table rows', FALSE, TRUE);
		$this->registerArgument('hideUntranslated', 'boolean', 'If FALSE, will NOT include elements which have NOT been translated, if current language is NOT the default language. Default is to show untranslated elements but never display the original if there is a translated version', FALSE, FALSE);
	}

	/**
	 * Get content records based on column and pid
	 *
	 * @param integer $limit
	 * @param string $order
	 * @return array
	 */
	protected function getContentRecords($limit = NULL, $order = NULL) {
		if (NULL === $limit && TRUE === isset($this->arguments['limit']) && FALSE === empty($this->arguments['limit'])) {
			$limit = $this->arguments['limit'];
		}
		if (NULL === $order && TRUE === isset($this->arguments['order']) && FALSE === empty($this->arguments['order'])) {
			$order = $this->arguments['order'];
		}
		$loadRegister = FALSE;
		if (empty($this->arguments['loadRegister']) === FALSE) {
			$this->contentObject->cObjGetSingle('LOAD_REGISTER', $this->arguments['loadRegister']);
			$loadRegister = TRUE;
		}
		$pid = $GLOBALS['TSFE']->id;
		$mountpointRange = '';
		if (isset($this->arguments['pageUid']) === TRUE && $this->arguments['pageUid'] > 0) {
			$pid = $this->arguments['pageUid'];
		} elseif ($GLOBALS['TSFE']->page['content_from_pid']) {
			$pid = $GLOBALS['TSFE']->page['content_from_pid'];
		}
		if (t3lib_div::_GP('MP') !== NULL) {
			$mountpointRange = t3lib_div::_GP('MP');
		}
		$order = $order . ' ' . $this->arguments['sortDirection'];
		$colPos = $this->arguments['column'];
		$contentUids = $this->arguments['contentUids'];
		$slide = $this->arguments['slide'] ? $this->arguments['slide'] : FALSE;
		$slideCollect = $this->arguments['slideCollect'] ? $this->arguments['slideCollect'] : FALSE;
		if ($slideCollect !== FALSE) {
			$slide = min($slide, $slideCollect);
		}
		$slideCollectReverse = $this->arguments['slideCollectReverse'];
		$rootLine = NULL;
		if ($slide) {
			$rootLine = $this->pageSelect->getRootLine($pid, $mountpointRange);
			if ($slideCollectReverse) {
				$rootLine = array_reverse($rootLine);
			}
		}

		$content = array();
		$hideUntranslated = (boolean) $this->arguments['hideUntranslated'];
		$currentLanguage = $GLOBALS['TSFE']->sys_language_uid;
		$languageCondition = '(sys_language_uid IN (-1,' . $currentLanguage . ')';
		if ($currentLanguage > 0) {
			if ($hideUntranslated) {
				$languageCondition .= ' AND l18n_parent > 0';
			}
			$nestedQuery = $GLOBALS['TYPO3_DB']->SELECTquery('l18n_parent', 'tt_content', 'sys_language_uid = ' . $currentLanguage . $GLOBALS['TSFE']->cObj->enableFields('tt_content'));
			$languageCondition .= ' AND uid NOT IN (' . $nestedQuery . ')';
		}
		$languageCondition .= ')';
		do {
			if ($slide) {
				$page = array_shift($rootLine);
				if (!$page) {
					break;
				}
				$pid = $page['uid'];
			}
			if (is_array($contentUids)) {
				$conditions = 'uid IN (' . implode(',', $contentUids) . ')';
			} else {
				$conditions = "pid = '" . $pid ."' AND colPos = '" . $colPos . "'" .
					$GLOBALS['TSFE']->cObj->enableFields('tt_content') .
					' AND ' . $languageCondition;
			}
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'tt_content', $conditions, 'uid', $order, $limit);
			if (TRUE === (boolean) $this->arguments['render'] && FALSE === empty($rows)) {
				$content = $this->getRenderedRecords($rows);
			} else {
				$content = $rows;
			}
			if (count($content) && !$slideCollect) {
				break;
			}
		} while ($slide !== FALSE && --$slide !== -1);

		if ($loadRegister) {
			$this->contentObject->cObjGetSingle('RESTORE_REGISTER', '');
		}

		return $content;
	}

	/**
	 * This function renders an array of tt_content record into an array of rendered content
	 * it returns a list of elements rendered by typoscript RECORD function
	 *
	 * @param array $rows database rows of records (each item is a tt_content table record)
	 * @return array
	 */
	protected function getRenderedRecords(array $rows) {
		$elements = array();
		foreach ($rows as $row) {
			$conf = array(
				'tables' => 'tt_content',
				'source' => $row['uid'],
				'dontCheckPid' => 1
			);
			array_push($elements, $GLOBALS['TSFE']->cObj->RECORDS($conf));
		}
		return $elements;
	}

}
