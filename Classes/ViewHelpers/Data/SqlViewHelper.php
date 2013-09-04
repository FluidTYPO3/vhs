<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2010 Claus Due <claus@wildside.dk>, Wildside A/S
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
 *
 * @author Claus Due, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers\Data
 */
class Tx_Vhs_ViewHelpers_Data_SqlViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	public function initializeArguments() {
		$this->registerArgument('as', 'string', 'Template variable name to use', FALSE, NULL);
		$this->registerArgument('query', 'string', 'Full query - or use individual arguments', FALSE, NULL);
		$this->registerArgument('table', 'string', 'Name of table for statement', FALSE, NULL);
		$this->registerArgument('fields', 'string', 'List (CSV) of fields for statement', FALSE, NULL);
		$this->registerArgument('condition', 'string', 'Conditions (SQL syntax) for statement', FALSE, NULL);
		$this->registerArgument('offset', 'mixed', 'Integer offset of statement', FALSE, NULL);
		$this->registerArgument('limit', 'mixed', 'Integer limit of statement', FALSE, NULL);
		$this->registerArgument('groupBy', 'string', 'Field to group by in GROUP BY statement', FALSE, NULL);
		$this->registerArgument('orderBy', 'string', 'Field to order by in ORDER statement', FALSE, NULL);
		$this->registerArgument('order', 'string', 'Which direction to order the results of statement', FALSE, NULL);
		$this->registerArgument('pruneResult', 'boolean', 'If TRUE, changes return type of result: one result row => row; one result cell => cell value; no result => "0"', FALSE, FALSE);
		$this->registerArgument('silent', 'boolean', 'If TRUE, returns the output instead of registering it as a template variable', FALSE, FALSE);
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$name = $this->arguments['as'];
		$query = $this->arguments['query'];
		$table = $this->arguments['table'];
		$fields = $this->arguments['fields'];
		$condition = $this->arguments['condition'];
		$offset = $this->arguments['offset'];
		$limit = $this->arguments['limit'];
		$groupBy = $this->arguments['groupBy'];
		$orderBy = $this->arguments['orderBy'];
		if ($orderBy && $this->arguments['order']) {
			$orderBy .= ' ' . $this->arguments['order'];
		}
		$silent = $this->arguments['silent'];

		if (!$query && !$table) {
			$query = $this->renderChildren();
		} else if ($table && !$query) {
			$query = $GLOBALS['TYPO3_DB']->SELECTquery($fields, $table, $condition, $groupBy, $orderBy, $limit, $offset);
		}
		$result = $GLOBALS['TYPO3_DB']->sql_query($query);
		if (!$result) {
			if ($silent) {
				// important force-return here to avoid error messages caused by processing of $result
				return NULL;
			} else {
				return '<div>Invalid SQL query! Error was: ' . $GLOBALS['TYPO3_DB']->sql_error(). '</div>';
			}
		}
		$rows = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			array_push($rows, $row);
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($result);
		if ($this->arguments['pruneResult'] && count($rows) === 0) {
			$value = '0';
		} else if ($this->arguments['pruneResult'] && count($rows) === 1) {
			$value = array_pop($rows);
			if (count($value) === 1) {
				$value = array_pop($value);
			}
		} else {
			$value = $rows;
		}
		if ($name === NULL) {
			if (!$silent) {
				return $value;
			}
		} else {
			if ($this->templateVariableContainer->exists($name)) {
				$this->templateVariableContainer->remove($name);
			}
			$this->templateVariableContainer->add($name, $value);
		}
		return NULL;
	}

}
