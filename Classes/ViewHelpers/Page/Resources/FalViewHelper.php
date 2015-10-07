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

}
