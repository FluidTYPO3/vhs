<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Philipp Kerling <pkerling@casix.org>
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
 * @author Philipp Kerling <pkerling@casix.org>
 * @package Vhs
 * @subpackage ViewHelpers
 */
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use FluidTYPO3\Vhs\ViewHelpers\PageRelatedRecordsViewHelperInterface;
use FluidTYPO3\Vhs\Service\PageSelectService;

/**
 * Abstract ViewHelper class for view helpers that want to support
 * sliding along the page rootline in order to find records. To use this
 * class, be sure to call initializeSlideArguments in your initializeArguments
 * function and have a class ready that implements PageRelatedRecordsViewHelperInterface.
 * It is used for actually finding any kind of records (e.g. content records)
 * on a given page. Everything related to sliding is then automatically
 * handled by getSlideRecords.
 */
abstract class AbstractSlideViewHelper extends AbstractViewHelper {

	/**
	 * @var \FluidTYPO3\Vhs\Service\PageSelectService
	 */
	protected $pageSelect;

	/**
	 * @param \FluidTYPO3\Vhs\Service\PageSelectService $pageSelect
	 * @return void
	 */
	public function injectPageSelectService(PageSelectService $pageSelect) {
		$this->pageSelect = $pageSelect;
	}

	/**
	 * Initialize arguments related to record sliding
	 * Please take note that the order argument is not automatically added
	 * as not all implementations can easily support it.
	 */
	public function initializeSlideArguments() {
		$this->registerArgument('limit', 'integer', 'Optional limit to the total number of records to render');
		$this->registerArgument('slide', 'integer', 'Enables Record Sliding - amount of levels which shall get walked up the rootline, including the current page. For infinite sliding (till the rootpage) set to -1. Only the first PID which has at minimum one record is used', FALSE, 0);
		$this->registerArgument('slideCollect', 'integer', 'Amount of levels which shall get walked up the rootline and have their records collected. For infinite sliding (till the rootpage) set to -1. If set, this value overrides $slide', FALSE, 0);
		$this->registerArgument('slideCollectReverse', 'boolean', 'Normally when collecting records the elements from the actual page get shown on the top and those from the parent pages below those. You can invert this behaviour (actual page elements at bottom) by setting this flag))', FALSE, 0);
	}
	
	/**
	 * Get records, optionally sliding up the page rootline
	 *
	 * @param integer $pageUid
	 * @param \FluidTYPO3\Vhs\ViewHelpers\PageRelatedRecordsViewHelperInterface $recordProvider
	 * @param integer $limit
	 * @param string $order
	 * @return array
	 */
	protected function getSlideRecords($pageUid, PageRelatedRecordsViewHelperInterface $recordProvider, $limit = NULL, $order = NULL) {
		if (NULL === $limit && FALSE === empty($this->arguments['limit'])) {
			$limit = (integer) $this->arguments['limit'];
		}
		
		$slide = (integer) $this->arguments['slide'];
		$slideCollectReverse = (boolean) $this->arguments['slideCollectReverse'];
		$slideCollect = (integer) $this->arguments['slideCollect'];
		if (FALSE === empty($this->arguments['slideCollect'])) {
			// $slideCollect overrides $slide to automatically start sliding if
			// collection is enabled.
			$slide = $slideCollect;
		}

		// find out which storage page UIDs to read from, respecting slide depth
		$storagePageUids = array();
		if (0 === $slide) {
			$storagePageUids[] = $pageUid;
		} else {
			$rootLine = $this->pageSelect->getRootLine($pageUid, NULL, $slideCollectReverse);
			if (-1 !== $slide) {
				if (TRUE === $slideCollectReverse) {
					$rootLine = array_slice($rootLine, - $slide);
				} else {
					$rootLine = array_slice($rootLine, 0, $slide);
				}
			}
			foreach ($rootLine as $page) {
				$storagePageUids[] = (integer) $page['uid'];
			}
		}
		// select records, respecting slide and slideCollect.
		$records = array();
		do {
			$storagePageUid = array_shift($storagePageUids);
			$limitRemaining = NULL;
			if (NULL !== $limit) {
				$limitRemaining = $limit - count($records);
				if (0 >= $limitRemaining) {
					break;
				}
			}
			$recordsFromPageUid = $recordProvider->getRecordsFromPage($storagePageUid, $limitRemaining, $order);
			if (0 < count($recordsFromPageUid)) {
				$records = array_merge($records, $recordsFromPageUid);
				if (0 === $slideCollect) {
					// stop collecting because argument said so and we've gotten at least one record now.
					break;
				}
			}
		} while (0 < count($storagePageUids));
		
		return $records;
	}

}
