<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Content\Random;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Content\AbstractContentViewHelper;

/**
 * ViewHelper for fetching a random content element in Fluid page templates
 *
 * @author Björn Fromme, <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Content\Random
 */
class GetViewHelper extends AbstractContentViewHelper {

	/**
	 * Initialize ViewHelper arguments
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->overrideArgument('limit', 'integer', 'Optional limit to the number of content elements to render', FALSE, 1);
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
		$limit = (integer) $this->arguments['limit'];
		$contentRecords = $this->getContentRecords($limit, 'RAND()');
		return $contentRecords;
	}

}
