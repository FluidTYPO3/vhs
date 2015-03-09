<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource\Record;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ResourceUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Resource\Record
 */
class FalViewHelper extends AbstractRecordResourceViewHelper {

	/**
	 * @var \TYPO3\CMS\Core\Resource\ResourceFactory
	 */
	protected $resourceFactory;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->resourceFactory = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\ResourceFactory');
	}

	/**
	 * @param mixed $identity
	 * @return mixed
	 */
	public function getResource($identity) {
		$fileReference = $this->resourceFactory->getFileReferenceObject(intval($identity));
		$file = $fileReference->getOriginalFile();
		$fileReferenceProperties = $fileReference->getProperties();
		$fileProperties = ResourceUtility::getFileArray($file);

		ArrayUtility::mergeRecursiveWithOverrule($fileProperties, $fileReferenceProperties, TRUE, FALSE, FALSE);
		return $fileProperties;
	}

	/**
	 * @param array $record
	 * @return array
	 */
	public function getResources($record) {
		$sqlTable = $GLOBALS['TYPO3_DB']->fullQuoteStr($this->getTable(), 'sys_file_reference');
		$sqlField = $GLOBALS['TYPO3_DB']->fullQuoteStr($this->getField(), 'sys_file_reference');
		$sqlRecordUid = $GLOBALS['TYPO3_DB']->fullQuoteStr($record[$this->idField], 'sys_file_reference');

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'sys_file_reference', 'deleted = 0 AND hidden = 0 AND tablenames = ' . $sqlTable . ' AND fieldname = ' . $sqlField . ' AND uid_foreign = ' . $sqlRecordUid, '', 'sorting_foreign');

		$resources = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$resources[] = $this->getResource($row['uid']);
		}

		return $resources;
	}

}
