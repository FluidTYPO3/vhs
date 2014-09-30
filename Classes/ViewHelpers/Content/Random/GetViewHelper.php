<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Content\Random;

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
 * ViewHelper for fetching a random content element in Fluid page templates
 *
 * @author Björn Fromme, <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Content\Random
 */
use FluidTYPO3\Vhs\ViewHelpers\Content\AbstractContentViewHelper;

class GetViewHelper extends AbstractContentViewHelper {
	
	/**
	 * Override limit argument default value
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->overrideArgument('limit', 'integer', 'Optional limit to the total number of records to render', FALSE, 1);
	}
	
	/**
	 * Render method
	 *
	 * @return mixed
	 */
	public function render() {
		if ('BE' === TYPO3_MODE) {
			return '';
		}
		// Remove limit for getContentRecords()
		$limit = $this->arguments['limit'];
		$this->arguments['limit'] = NULL;
		// Just using getContentRecords with a limit of 1 would not support
		// using slideCollect as collecting would stop as soon as one record
		// was found. Using $render = FALSE with getContentRecords will save us
		// rendering all content elements that end up unused anyway.
		$contentRecords = $this->getContentRecords(NULL, NULL, FALSE);
		if (FALSE === empty($contentRecords)) {
			shuffle($contentRecords);
			$contentRecords = array_slice($contentRecords, 0, $limit);
			if (TRUE === (boolean) $this->arguments['render']) {
				$contentRecords = $this->getRenderedRecords($contentRecords);
			}
		}
		return $contentRecords;
	}

}
