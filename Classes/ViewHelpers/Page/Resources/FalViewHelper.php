<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Resources;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Resource\Record\FalViewHelper as ResourcesFalViewHelper;
use FluidTYPO3\Vhs\Traits\SlideViewHelperTrait;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Page\Resources
 */
class FalViewHelper extends ResourcesFalViewHelper {

	use SlideViewHelperTrait;

	const defaultTable = 'pages';
	const defaultField = 'media';

	/**
	 * @var string
	 */
	protected $table = self::defaultTable;

	/**
	 * @var string
	 */
	protected $field = self::defaultField;

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		parent::initializeArguments();

		$this->overrideArgument('table', 'string', 'The table to lookup records.', FALSE, self::defaultTable);
		$this->overrideArgument('field', 'string', 'The field of the table associated to resources.', FALSE, self::defaultField);
		$this->registerSlideArguments();
	}

	/**
	 * @param integer $pageUid
	 * @param integer $limit
	 * @return array
	 */
	protected function getSlideRecordsFromPage($pageUid, $limit) {
		$resources = $this->getResources($this->getRecord($pageUid));
		if (NULL !== $limit && count($resources) > $limit) {
			$resources = array_slice($resources, 0, $limit);
		}
		return $resources;
	}

	/**
	 * AbstractRecordResource usually uses the current cObj as reference,
	 * but the page is needed here
	 *
	 * @return array
	 */
	public function getActiveRecord() {
		return $GLOBALS['TSFE']->page;
	}

	/**
	 * @return mixed
	 * @throws Exception
	 */
	public function render() {
		$record = $this->arguments['record'];
		$uid = $this->arguments['uid'];
		if (NULL === $uid) {
			if (NULL === $record) {
				$record = $this->getActiveRecord();
			}
			$uid = $record['uid'];
		}
		if (NULL === $uid) {
			throw new Exception('No record was found. The "record" or "uid" argument must be specified.', 1384611413);
		}
		$resources = $this->getSlideRecords($uid);

		return $this->renderChildrenWithVariableOrReturnInput($resources);
	}

	/**
	 * Table is either pages or pages_language_overlay
	 *
	 * This is kind of unique for the pages table, so override
	 * the base method here.
	 *
	 * @override
	 * @return string
	 */
	public function getTable() {
		return $GLOBALS['TSFE']->sys_language_uid === 0 ? 'pages' : 'pages_language_overlay';
	}

	/**
	 * @override
	 * @param mixed $id
	 * @return array
	 */
	public function getRecord($id) {
		$table = $this->getTable();
		$idField = $this->getTable() === 'pages_language_overlay' ? 'pid' : $this->idField;

		$sqlIdField = $GLOBALS['TYPO3_DB']->quoteStr($idField, $table);
		$sqlId = $GLOBALS['TYPO3_DB']->fullQuoteStr($id, $table);

		return reset($GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', $table, $sqlIdField . ' = ' . $sqlId));
	}
}
