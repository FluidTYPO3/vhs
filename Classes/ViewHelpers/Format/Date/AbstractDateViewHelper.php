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
 * Base class: Date ViewHelpers
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Format\Date
 */
abstract class Tx_Vhs_ViewHelpers_Format_Date_AbstractDateViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var boolean
	 */
	protected $escapingInterceptorEnabled = FALSE;

	/**
	 * @param mixed $date
	 * @return \DateTime
	 * @throws Tx_Fluid_Core_ViewHelper_Exception
	 */
	protected function enforceDateTime($date) {
		if (FALSE === ($date instanceof \DateTime)) {
			try {
				if (is_integer($date)) {
					$date = new \DateTime('@' . $date);
				} else {
					$date = new \DateTime($date);
				}
			} catch (\Exception $exception) {
				throw new Tx_Fluid_Core_ViewHelper_Exception('"' . $date . '" could not be parsed by \DateTime constructor.', 1369399931);
			}
		}
		return $date;
	}

	/**
	 * @param \DateTime $date
	 * @param string $format
	 * @return string
	 */
	protected function formatDate($date, $format = 'Y-m-d') {
		if (strpos($format, '%') !== FALSE) {
			return strftime($format, $date->format('U'));
		} else {
			return $date->format($format);
		}
	}

}
