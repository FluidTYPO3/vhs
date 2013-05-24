<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * ### Date interval ViewHelper
 *
 * Returns one of the following depending on provided arguments:
 *
 * - a DateInterval object in case only an interval string is provided
 * - a string representing the DateInterval formatted by the provided
 *   format string
 * - a string representing the DateInterval applied to the provided
 *   date (which can be a Date object or any date and time format
 *   consumable by DateTime's constructor) formatted by the provided
 *   date format
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Format\Date
 */
class Tx_Vhs_ViewHelpers_Format_Date_IntervalViewHelper extends Tx_Vhs_ViewHelpers_Format_Date_AbstractDateViewHelper {

	/**
	 * @param string $interval
	 * @param string $intervalFormat
	 * @param mixed $date
	 * @param string $dateFormat
	 * @throws Tx_Fluid_Core_ViewHelper_Exception
	 * @return string
	 */
	public function render($interval, $intervalFormat = NULL, $date = NULL, $dateFormat = 'Y-m-d') {
		$interval = new \DateInterval($interval);
		if (NULL === $date) {
			if (NULL !== $intervalFormat && '' !== $intervalFormat) {
				$output = $interval->format($intervalFormat);
			} else {
				$output = $interval;
			}
		} else {
			$date = $this->enforceDateTime($date);
			$date->add($interval);
			$output = $this->formatDate($date, $dateFormat);
		}

		return $output;
	}

}
