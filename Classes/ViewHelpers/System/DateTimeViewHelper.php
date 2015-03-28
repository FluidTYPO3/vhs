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
 * ### System: DateTime
 *
 * Returns the current system UNIX timestamp as DateTime.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\System
 */
class DateTimeViewHelper extends AbstractViewHelper {

	/**
	 * @return \DateTime
	 */
	public function render() {
		return \DateTime::createFromFormat('U', $this->getTimestamp());
	}

	/**
	 * @return integer
	 */
	protected function getTimestamp() {
		return time();
	}

}
