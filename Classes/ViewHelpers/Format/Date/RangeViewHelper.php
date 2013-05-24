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
 * ### Date range ViewHelper
 *
 * Returns a string of formatted dates representing a date range which is
 * concatenated by a configurable glue string and optional "space glue".
 * Start and end date can be either DateTime objects or any date and time
 * format consumable by DateTime's constructor. Start date defaults to
 * current date if omitted. Format string for start date defaults to the
 * end date's format if omitted.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Format\Date
 */
class Tx_Vhs_ViewHelpers_Format_Date_RangeViewHelper extends Tx_Vhs_ViewHelpers_Format_Date_AbstractDateViewHelper {

	/**
	 * @param mixed $end
	 * @param mixed $start
	 * @param string $formatStart
	 * @param string $formatEnd
	 * @param string $glue
	 * @param bool $spaceGlue
	 * @throws Tx_Fluid_Core_ViewHelper_Exception
	 * @return string
	 */
	public function render($end, $start = NULL, $formatStart = NULL, $formatEnd = 'Y-m-d', $glue = '-', $spaceGlue = TRUE) {
		if (NULL === $start) {
			$start = new \DateTime('now');
		} else {
			$start = $this->enforceDateTime($start);
		}
		$end = $this->enforceDateTime($end);
		if (NULL === $formatStart) {
			$formatStart = $formatEnd;
		}
		$output = $this->formatDate($start, $formatStart);
		$output .= TRUE === $spaceGlue ? ' ' : '';
		$output .= $glue;
		$output .= TRUE === $spaceGlue ? ' ' : '';
		$output .= $this->formatDate($end, $formatEnd);
		return $output;
	}

}
