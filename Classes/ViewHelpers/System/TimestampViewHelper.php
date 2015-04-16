<?php
namespace FluidTYPO3\Vhs\ViewHelpers\System;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### System: UNIX Timestamp
 *
 * Returns the current system UNIX timestamp as integer.
 * Useful combined with the Math group of ViewHelpers:
 *
 *     <!-- adds exactly one hour to a DateTime and formats it -->
 *     <f:format.date format="H:i">{dateTime.timestamp -> v:math.sum(b: 3600)}</f:format.date>
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\System
 */
class TimestampViewHelper extends AbstractViewHelper {

	/**
	 * @return integer
	 */
	public function render() {
		return $this->getTimestamp();
	}

	/**
	 * @return integer
	 */
	protected function getTimestamp() {
		return time();
	}

}
