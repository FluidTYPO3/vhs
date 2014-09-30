<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Resources;

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
use FluidTYPO3\Vhs\ViewHelpers\Resource\Record\FalViewHelper as ResourcesFalViewHelper;
use FluidTYPO3\Vhs\ViewHelpers\PageRelatedRecordsViewHelperInterface;

/**
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Page\Resources
 */
class FalViewHelper extends ResourcesFalViewHelper implements PageRelatedRecordsViewHelperInterface {

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
		$this->initializeSlideArguments();

		$this->overrideArgument('table', 'string', 'The table to lookup records.', FALSE, self::defaultTable);
		$this->overrideArgument('field', 'string', 'The field of the table associated to resources.', FALSE, self::defaultField);
	}
	
	/**
	 * @param integer $pageUid
	 * @param integer $limit
	 * @param string $order ignored
	 * @return array
	 */
	public function getRecordsFromPage($pageUid, $limit = NULL, $order = NULL) {
		$resources = $this->getResources($this->getRecord($pageUid));
		if (NULL !== $limit && count($resources) > $limit) {
			$resources = array_slice($resources, 0, $limit);
		}
		return $resources;
	}
	
	/**
	 * @return mixed
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

		$resources = $this->getSlideRecords($uid, $this);

		$as = $this->arguments['as'];
		if (TRUE === empty($as)) {
			return $resources;
		}

		$variables = array($as => $resources);
		$output = ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);
		return $output;
	}

}
