<?php
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
abstract class Tx_Vhs_ViewHelpers_Resource_Record_AbstractRecordResourceViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper implements Tx_Vhs_ViewHelpers_Resource_Record_RecordResourceViewHelperInterface {

	/**
	 * @var string
	 */
	protected $table;

	/**
	 * @var string
	 */
	protected $field;

	/**
	 * @var string
	 */
	protected $idField = 'uid';

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerArgument('table', 'string', 'The table to lookup records.', TRUE);
		$this->registerArgument('field', 'string', 'The field of the table associated to resources.', TRUE);

		$this->registerArgument('record', 'array', 'The actual record. Alternatively you can use the "uid" argument.', FALSE, NULL);
		$this->registerArgument('uid', 'integer', 'The uid of the record. Alternatively you can use the "record" argument.', FALSE, NULL);

		$this->registerArgument('as', 'string', 'If specified, a template variable with this name containing the requested data will be inserted instead of returning it.', FALSE, NULL);
	}

	/**
	 * @param mixed $identity
	 * @return mixed
	 */
	public function getResource($identity) {
		return $identity;
	}

	/**
	 * @param array $record
	 * @return array
	 */
	public function getResources($record) {
		$field = $this->getField();

		if (FALSE === isset($record[$field])) {
			throw new Tx_Fluid_Core_ViewHelper_Exception('The "field" argument was not found on the selected record.', 1384612728);
		}

		if (TRUE === empty($record[$field])) {
			return array();
		}

		return \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $record[$field]);
	}

	/**
	 * @return string
	 */
	public function getTable() {
		$table = $this->arguments['table'];
		if (NULL === $table) {
			$table = $this->table;
		}

		if (TRUE === empty($table) || FALSE === is_string($table)) {
			throw new Tx_Fluid_Core_ViewHelper_Exception('The "table" argument must be specified and must be a string.', 1384611336);
		}

		return $table;
	}

	/**
	 * @return string
	 */
	public function getField() {
		$field = $this->arguments['field'];
		if (NULL === $field) {
			$field = $this->field;
		}

		if (TRUE === empty($field) || FALSE === is_string($field)) {
			throw new Tx_Fluid_Core_ViewHelper_Exception('The "field" argument must be specified and must be a string.', 1384611355);
		}

		return $field;
	}

	/**
	 * @param mixed $id
	 * @return array
	 */
	public function getRecord($id) {
		$table = $this->getTable();
		$idField = $this->idField;

		$sqlIdField = $GLOBALS['TYPO3_DB']->quoteStr($idField, $table);
		$sqlId = $GLOBALS['TYPO3_DB']->fullQuoteStr($id, $table);

		return reset($GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', $table, $sqlIdField . ' = ' . $sqlId));
	}

	/**
	 * @return array
	 */
	public function getActiveRecord() {
		return $this->configurationManager->getContentObject()->data;
	}

	/**
	 * @return mixed
	 */
	public function render() {
		$record = $this->arguments['record'];
		$uid = $this->arguments['uid'];

		if (NULL === $record) {
			if (NULL === $uid) {
				$record = $this->getActiveRecord();
			} else {
				$record = $this->getRecord($uid);
			}
		}

		if (NULL === $record) {
			throw new Tx_Fluid_Core_ViewHelper_Exception('No record was found. The "record" or "uid" argument must be specified.', 1384611413);
		}

		$resources = $this->getResources($record);

		$as = $this->arguments['as'];
		if (TRUE === empty($as)) {
			return $resources;
		}

		$variables = array($as => $resources);
		$output = Tx_Vhs_Utility_ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);
		return $output;
	}

}
