<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;
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
 ***************************************************************/

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * ### Date range calculation/formatting ViewHelper
 *
 * Uses DateTime and DateInterval operations to calculate a range
 * between two DateTimes.
 *
 * #### Usages
 *
 * - As formatter, the ViewHelper can output a string value such as
 *   "2013-04-30 - 2013-05-30" where you can configure both the start
 *   and end date (or their common) formats as well as the "glue"
 *   which binds the two dates together.
 * - As interval calculator, the ViewHelper can be used with a special
 *   "intervalFormat" which is a string used in the constructor method
 *   for the DateInterval class - for example, "P3M" to add three months.
 *   Used this way, you can specify the start date (or rely on the
 *   default "now" DateTime) and specify the "intervalFormat" to add
 *   your desired duration to your starting date and use that as end
 *   date. Without the "return" attribute, this mode simply outputs
 *   the formatted dates with interval deciding the end date.
 * - When used with the "return" attribute you can specify which type
 *   of data to return:
 *   - if "return" is "DateTime", a single DateTime instance is returned
 *     (which is the end date). Use this with a start date to return the
 *     DateTime corresponding to "intervalFormat" into the future/past.
 *   - if "return" is a string such as "w", "d", "h" etc. the corresponding
 *     counter value (weeks, days, hours etc.) is returned.
 *   - if "return" is an array of counter IDs, for example Array("w", "d"),
 *     the corresponding counters from the range are returned as an array.
 *
 * #### Note about LLL support and array consumers
 *
 * When used with the "return" attribute and when this attribute is an
 * array, the output becomes suitable for consumption by f:translate, v:l
 * or f:format.sprintf for example - as the "arguments" attribute:
 *
 *     <f:translate key="myDateDisplay"
 *         arguments="{v:format.dateRange(intervalFormat: 'P3W', return: {0: 'w', 1: 'd'})}"
 *     />
 *
 * Which if "myDateDisplay" is a string such as "Deadline: %d week(s) and
 * %d day(s)" would output a result such as "Deadline: 4 week(s) and 2 day(s)".
 *
 * > Tip: the values returned by this ViewHelper in both array and single
 * > value return modes, are also nicely consumable by the "math" suite
 * > of ViewHelpers, for example `v:math.division` would be able to divide
 * > number of days by two, three etc. to further divide the date range.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Format
 */
class DateRangeViewHelper extends AbstractViewHelper {

	/**
	 * @var boolean
	 */
	protected $escapingInterceptorEnabled = FALSE;

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('start', 'mixed', 'Start date which can be a DateTime object or a string consumable by DateTime constructor', FALSE, 'now');
		$this->registerArgument('end', 'mixed', 'End date which can be a DateTime object or a string consumable by DateTime constructor', FALSE, NULL);
		$this->registerArgument('intervalFormat', 'string', 'Interval format consumable by DateInterval', FALSE, NULL);
		$this->registerArgument('format', 'string', 'Date format to apply to both start and end date', FALSE, 'Y-m-d');
		$this->registerArgument('startFormat', 'string', 'Date format to apply to start date', FALSE, NULL);
		$this->registerArgument('endFormat', 'string', 'Date format to apply to end date', FALSE, NULL);
		$this->registerArgument('glue', 'string', 'Glue string to concatenate dates with', FALSE, '-');
		$this->registerArgument('spaceGlue', 'boolean', 'If TRUE glue string is surrounded with whitespace', FALSE, TRUE);
		$this->registerArgument('return', 'mixed', 'Return type; can be exactly "DateTime" to return a DateTime instance, a string like "w" ' .
			'or "d" to return weeks, days between the two dates - or an array of w, d, etc. strings to return the corresponding range count values as an array.', FALSE, NULL);
	}

	/**
	 * Render method
	 *
	 * @throws Exception
	 * @return mixed
	 */
	public function render() {
		if (TRUE === isset($this->arguments['start']) && FALSE === empty($this->arguments['start'])) {
			$start = $this->arguments['start'];
		} else {
			$start = 'now';
		}
		$startDateTime = $this->enforceDateTime($start);

		if (TRUE === isset($this->arguments['end']) && FALSE === empty($this->arguments['end'])) {
			$endDateTime = $this->enforceDateTime($this->arguments['end']);
		} else {
			$endDateTime = NULL;
		}

		if (TRUE === isset($this->arguments['intervalFormat']) && FALSE === empty($this->arguments['intervalFormat'])) {
			$intervalFormat = $this->arguments['intervalFormat'];
		}

		if (NULL === $intervalFormat && NULL === $endDateTime) {
			throw new Exception('Either end or intervalFormat has to be provided.', 1369573110);
		}

		if (TRUE === isset($intervalFormat) && NULL !== $intervalFormat) {
			try {
				$interval = new \DateInterval($intervalFormat);
			} catch (\Exception $exception) {
				throw new Exception('"' . $intervalFormat . '" could not be parsed by \DateInterval constructor.', 1369573111);
			}
		} else {
			$interval = $endDateTime->diff($startDateTime);
		}

		if (NULL !== $interval && NULL === $endDateTime) {
			$endDateTime = new \DateTime();
			$endDateTime->add($endDateTime->diff($startDateTime));
			$endDateTime->add($interval);
		}

		$return = $this->arguments['return'];
		if (NULL === $return) {
			$spaceGlue = (boolean) $this->arguments['spaceGlue'];
			$glue = strval($this->arguments['glue']);
			$startFormat = $this->arguments['format'];
			$endFormat = $this->arguments['format'];
			if (NULL !== $this->arguments['startFormat'] && FALSE === empty($this->arguments['startFormat'])) {
				$startFormat = $this->arguments['startFormat'];
			}
			if (NULL !== $this->arguments['endFormat'] && FALSE === empty($this->arguments['endFormat'])) {
				$endFormat = $this->arguments['endFormat'];
			}
			$output  = $this->formatDate($startDateTime, $startFormat);
			$output .= TRUE === $spaceGlue ? ' ' : '';
			$output .= $glue;
			$output .= TRUE === $spaceGlue ? ' ' : '';
			$output .= $this->formatDate($endDateTime, $endFormat);
		} elseif ('DateTime' === $return) {
			$output = $endDateTime;
		} elseif (TRUE === is_string($return)) {
			if (FALSE === strpos($return, '%')) {
				$return = '%' . $return;
			}
			$output = $interval->format($return);
		} elseif (TRUE === is_array($return)) {
			$output = array();
			foreach ($return as $format) {
				if (FALSE === strpos($format, '%')) {
					$format = '%' . $format;
				}
				array_push($output, $interval->format($format));
			}
		}
		return $output;
	}

	/**
	 * @param mixed $date
	 * @return \DateTime
	 * @throws Exception
	 */
	protected function enforceDateTime($date) {
		if (FALSE === $date instanceof \DateTime) {
			try {
				if (TRUE === is_integer($date)) {
					$date = new \DateTime('@' . $date);
				} else {
					$date = new \DateTime($date);
				}
				$date->setTimezone(new \DateTimeZone(date_default_timezone_get()));
			} catch (\Exception $exception) {
				throw new Exception('"' . $date . '" could not be parsed by \DateTime constructor.', 1369573112);
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
		if (FALSE !== strpos($format, '%')) {
			return strftime($format, $date->format('U'));
		} else {
			return $date->format($format);
		}
	}

}
