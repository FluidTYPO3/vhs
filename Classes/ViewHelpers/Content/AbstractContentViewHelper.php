<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Content;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
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
 * @author Claus Due <claus@namelesscoder.net>
 * @author Dominique Feyer, <dfeyer@ttree.ch>
 * @author Daniel Schöne, <daniel@schoene.it>
 * @author Björn Fromme, <fromme@dreipunktnull.com>, dreipunktnull
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Content
 */
use FluidTYPO3\Vhs\ViewHelpers\AbstractSlideViewHelper;
use FluidTYPO3\Vhs\ViewHelpers\PageRelatedRecordsViewHelperInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use FluidTYPO3\Vhs\Service\PageSelectService;

abstract class AbstractContentViewHelper extends AbstractSlideViewHelper implements PageRelatedRecordsViewHelperInterface {

	/**
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	protected $contentObject;

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
		$this->contentObject = $configurationManager->getContentObject();
	}

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		$this->initializeSlideArguments();
		$this->registerArgument('column', 'integer', 'Name of the column to render', FALSE, 0);
		$this->registerArgument('order', 'string', 'Optional sort field of content elements - RAND() supported', FALSE, 'sorting');
		$this->registerArgument('sortDirection', 'string', 'Optional sort direction of content elements', FALSE, 'ASC');
		$this->registerArgument('pageUid', 'integer', 'If set, selects only content from this page UID', FALSE, 0);
		$this->registerArgument('contentUids', 'array', 'If used, replaces all conditions with an "uid IN (1,2,3)" style condition using the UID values from this array');
		$this->registerArgument('sectionIndexOnly', 'boolean', 'If TRUE, only renders/gets content that is marked as "include in section index"', FALSE, FALSE);
		$this->registerArgument('loadRegister', 'array', 'List of LOAD_REGISTER variable');
		$this->registerArgument('render', 'boolean', 'Optional returning variable as original table rows', FALSE, TRUE);
		$this->registerArgument('hideUntranslated', 'boolean', 'If FALSE, will NOT include elements which have NOT been translated, if current language is NOT the default language. Default is to show untranslated elements but never display the original if there is a translated version', FALSE, FALSE);
	}

	/**
	 * Get content records based on column and pid
	 *
	 * @param integer $limit
	 * @param string $order
	 * @param boolean $render
	 * @return array
	 */
	protected function getContentRecords($limit = NULL, $order = NULL, $render = NULL) {
		$pageUid = $this->getPageUid();
		$contentRecords = $this->getSlideRecords($pageUid, $this, $limit, $order);

		if (NULL === $render && FALSE === empty($this->arguments['render'])) {
			$render = $this->arguments['render'];
		}
		if (TRUE === (boolean) $render) {
			$contentRecords = $this->getRenderedRecords($contentRecords);
		} else {
			$contentRecords = $contentRecords;
		}

		return $contentRecords;
	}

	/**
	 * @param integer $pageUid
	 * @param integer $limit
	 * @param string $order
	 * @return array[]
	 */
	public function getRecordsFromPage($pageUid, $limit = NULL, $order = NULL) {
		$column = (integer) $this->arguments['column'];
		if (NULL === $limit && FALSE === empty($this->arguments['limit'])) {
			$limit = (integer) $this->arguments['limit'];
		}
		if (NULL === $order && FALSE === empty($this->arguments['order'])) {
			$order = $this->arguments['order'];
		}
		if (FALSE === empty($order)) {
			$sortDirection = strtoupper(trim($this->arguments['sortDirection']));
			if ('ASC' !== $sortDirection && 'DESC' !== $sortDirection) {
				$sortDirection = 'ASC';
			}
			$order = $order . ' ' . $sortDirection;
		}
		$contentUids = $this->arguments['contentUids'];
		if (TRUE === is_array($contentUids)) {
			$conditions = 'uid IN (' . implode(',', $contentUids) . ')';
		} else {
			$hideUntranslated = (boolean) $this->arguments['hideUntranslated'];
			$currentLanguage = $GLOBALS['TSFE']->sys_language_content;
			$languageCondition = '(sys_language_uid IN (-1,' . $currentLanguage . ')';
			if (0 < $currentLanguage) {
				if (TRUE === $hideUntranslated) {
					$languageCondition .= ' AND l18n_parent > 0';
				}
				$nestedQuery = $GLOBALS['TYPO3_DB']->SELECTquery('l18n_parent', 'tt_content', 'sys_language_uid = ' .
					$currentLanguage . $GLOBALS['TSFE']->cObj->enableFields('tt_content'));
				$languageCondition .= ' AND uid NOT IN (' . $nestedQuery . ')';
			}
			$languageCondition .= ')';
			$conditions = "pid = '" . (integer) $pageUid . "' AND colPos = '" . (integer) $column . "'" .
				$GLOBALS['TSFE']->cObj->enableFields('tt_content') . ' AND ' . $languageCondition;
		}
		if (TRUE === (boolean) $this->arguments['sectionIndexOnly']) {
			$conditions .= ' AND sectionIndex = 1';
		}

		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'tt_content', $conditions, 'uid', $order, $limit);
		return $rows;
	}

	/**
	 * Gets the configured, or the current page UID if
	 * none is configured in arguments and no content_from_pid
	 * value exists in the current page record's attributes.
	 *
	 * @return integer
	 */
	protected function getPageUid() {
		$pageUid = (integer) $this->arguments['pageUid'];
		if (1 > $pageUid) {
			$pageUid = (integer) $GLOBALS['TSFE']->page['content_from_pid'];
		}
		if (1 > $pageUid) {
			$pageUid = (integer) $GLOBALS['TSFE']->id;
		}
		return $pageUid;
	}

	/**
	 * This function renders an array of tt_content record into an array of rendered content
	 * it returns a list of elements rendered by typoscript RECORD function
	 *
	 * @param array $rows database rows of records (each item is a tt_content table record)
	 * @return array
	 */
	protected function getRenderedRecords(array $rows) {
		if (FALSE === empty($this->arguments['loadRegister'])) {
			$this->contentObject->cObjGetSingle('LOAD_REGISTER', $this->arguments['loadRegister']);
		}
		$elements = array();
		foreach ($rows as $row) {
			array_push($elements, $this->renderRecord($row));
		}
		if (FALSE === empty($this->arguments['loadRegister'])) {
			$this->contentObject->cObjGetSingle('RESTORE_REGISTER', '');
		}
		return $elements;
	}

	/**
	 * This function renders a raw tt_content record into the corresponding
	 * element by typoscript RENDER function. We keep track of already
	 * rendered records to avoid rendering the same record twice inside the
	 * same nested stack of content elements.
	 *
	 * @param array $row
	 * @return string|NULL
	 */
	protected function renderRecord(array $row) {
		if (0 < $GLOBALS['TSFE']->recordRegister['tt_content:' . $row['uid']]) {
			return NULL;
		}
		$conf = array(
			'tables' => 'tt_content',
			'source' => $row['uid'],
			'dontCheckPid' => 1
		);
		$parent = $GLOBALS['TSFE']->currentRecord;
		// If the currentRecord is set, we register, that this record has invoked this function.
		// It's should not be allowed to do this again then!!
		if (FALSE === empty($parent)) {
			++$GLOBALS['TSFE']->recordRegister[$parent];
		}
		$html = $GLOBALS['TSFE']->cObj->RECORDS($conf);
		$GLOBALS['TSFE']->currentRecord = $parent;
		if (FALSE === empty($parent)) {
			--$GLOBALS['TSFE']->recordRegister[$parent];
		}
		return $html;
	}

}
