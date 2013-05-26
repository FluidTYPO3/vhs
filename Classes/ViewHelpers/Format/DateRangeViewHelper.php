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
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Format\Date
 */
class Tx_Vhs_ViewHelpers_Format_DateRangeViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var boolean
	 */
	protected $escapingInterceptorEnabled = FALSE;

	/**
	 * @var \DateTime
	 */
	protected $startDateTime;

	/**
	 * @var \DateTime
	 */
	protected $endDateTime;

	/**
	 * @var \DateInterval
	 */
	protected $interval;

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('start', 'mixed', 'Start date which can be a DateTime object or a string consumable by DateTime constructor', FALSE, 'now');
		$this->registerArgument('end', 'mixed', 'End date which can be a DateTime object or a string consumable by DateTime constructor', FALSE, NULL);
		$this->registerArgument('intervalFormat', 'string', 'Interval format consumable by DateInterval', FALSE, NULL);
		$this->registerArgument('dateFormat', 'string', 'Date format to apply to both start and end date', FALSE, 'Y-m-d');
		$this->registerArgument('startFormat', 'string', 'Date format to apply to start date', FALSE, NULL);
		$this->registerArgument('endFormat', 'string', 'Date format to apply to end date', FALSE, NULL);
		$this->registerArgument('glue', 'string', 'Glue string to concatenate dates with', FALSE, '-');
		$this->registerArgument('spaceGlue', 'boolean', 'If TRUE glue string is surrounded with whitespace', FALSE, TRUE);
		$this->registerArgument('return', 'mixed', '', FALSE, NULL);
	}

	/**
	 * Render method
	 *
	 * @throws Tx_Fluid_Core_ViewHelper_Exception
	 * @return mixed
	 */
	public function render() {
		if (TRUE === isset($this->arguments['start']) && FALSE === empty($this->arguments['start'])) {
			$start = $this->arguments['start'];
		} else {
			$start = 'now';
		}
		$this->startDateTime = $this->enforceDateTime($start);

		if (TRUE === isset($this->arguments['end']) && FALSE === empty($this->arguments['end'])) {
			$this->endDateTime = $this->enforceDateTime($this->arguments['end']);
		}

		if (TRUE === isset($this->arguments['intervalFormat']) && FALSE === empty($this->arguments['intervalFormat'])) {
			$intervalFormat = $this->arguments['intervalFormat'];
		}

		if (NULL === $intervalFormat && NULL === $this->endDateTime) {
			throw new Tx_Fluid_Core_ViewHelper_Exception('Either end or intervalFormat has to be provided.', 12345);
		}

		if (TRUE === isset($intervalFormat) && NULL !== $intervalFormat) {
			try {
				$this->interval = new \DateInterval($intervalFormat);
			} catch (\Exception $exception) {
				throw new Tx_Fluid_Core_ViewHelper_Exception('"' . $intervalFormat . '" could not be parsed by \DateInterval constructor.', 123456);
			}
		} else {
			$this->interval = $this->endDateTime->diff($this->startDateTime);
		}

		if (NULL !== $this->interval && NULL === $this->endDateTime) {
			$this->endDateTime = clone($this->startDateTime);
			$this->endDateTime->add($this->interval);
		}

		$return = $this->arguments['return'];
		if (NULL === $return) {
			$spaceGlue = (boolean) $this->arguments['spaceGlue'];
			$glue = strval($this->arguments['glue']);
			$startFormat = $endFormat = $this->arguments['dateFormat'];
			if (NULL !== $this->arguments['startFormat'] && FALSE === empty($this->arguments['startFormat'])) {
				$startFormat = $this->arguments['startFormat'];
			}
			if (NULL !== $this->arguments['endFormat'] && FALSE === empty($this->arguments['endFormat'])) {
				$endFormat = $this->arguments['endFormat'];
			}
			$output  = $this->formatDate($this->startDateTime, $startFormat);
			$output .= TRUE === $spaceGlue ? ' ' : '';
			$output .= $glue;
			$output .= TRUE === $spaceGlue ? ' ' : '';
			$output .= $this->formatDate($this->endDateTime, $endFormat);
		} elseif ('DateTime' === $return) {
			$output = $this->endDateTime;
		} elseif (TRUE === is_string($return)) {
			if (FALSE === strpos($return, '%')) {
				$return = '%' . $return;
			}
			$output = $this->interval->format($return);
		} elseif (TRUE === is_array($return)) {
			$output = array();
			foreach ($return as $format) {
				if (FALSE === strpos($format, '%')) {
					$format = '%' . $format;
				}
				array_push($output, $this->interval->format($format));
			}
		}
		return $output;
	}

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
