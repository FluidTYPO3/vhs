<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource\Record;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
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
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Resource\Record
 */
use TYPO3\CMS\Core\Utility\GeneralUtility;
use FluidTYPO3\Vhs\Utility\ResourceUtility;

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

		\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($fileProperties, $fileReferenceProperties, TRUE, FALSE, FALSE);
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
