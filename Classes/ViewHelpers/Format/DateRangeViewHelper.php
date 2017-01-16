<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ErrorUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

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
 *   - if "return" is an array of counter IDs, for example ["w", "d"],
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
 */
class DateRangeViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @var boolean
     */
    protected $escapingInterceptorEnabled = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument(
            'start',
            'mixed',
            'Start date which can be a DateTime object or a string consumable by DateTime constructor',
            false,
            'now'
        );
        $this->registerArgument(
            'end',
            'mixed',
            'End date which can be a DateTime object or a string consumable by DateTime constructor'
        );
        $this->registerArgument(
            'intervalFormat',
            'string',
            'Interval format consumable by DateInterval'
        );
        $this->registerArgument(
            'format',
            'string',
            'Date format to apply to both start and end date',
            false,
            'Y-m-d'
        );
        $this->registerArgument('startFormat', 'string', 'Date format to apply to start date');
        $this->registerArgument('endFormat', 'string', 'Date format to apply to end date');
        $this->registerArgument('glue', 'string', 'Glue string to concatenate dates with', false, '-');
        $this->registerArgument(
            'spaceGlue',
            'boolean',
            'If TRUE glue string is surrounded with whitespace',
            false,
            true
        );
        $this->registerArgument(
            'return',
            'mixed',
            'Return type; can be exactly "DateTime" to return a DateTime instance, a string like "w" or "d" to ' .
            'return weeks, days between the two dates - or an array of w, d, etc. strings to return the ' .
            'corresponding range count values as an array.'
        );
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     * @throws Exception
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $start = $renderChildrenClosure();
        if (empty($arguments['start'])) {
            $start = 'now';
        }
        $startDateTime = static::enforceDateTime($start);

        if (true === isset($arguments['end']) && false === empty($arguments['end'])) {
            $endDateTime = static::enforceDateTime($arguments['end']);
        } else {
            $endDateTime = null;
        }

        if (true === isset($arguments['intervalFormat']) && false === empty($arguments['intervalFormat'])) {
            $intervalFormat = $arguments['intervalFormat'];
        }

        if (null === $intervalFormat && null === $endDateTime) {
            ErrorUtility::throwViewHelperException('Either end or intervalFormat has to be provided.', 1369573110);
        }

        if (true === isset($intervalFormat) && null !== $intervalFormat) {
            try {
                $interval = new \DateInterval($intervalFormat);
            } catch (\Exception $exception) {
                ErrorUtility::throwViewHelperException(
                    '"' . $intervalFormat . '" could not be parsed by \DateInterval constructor.',
                    1369573111
                );
            }
        } else {
            $interval = $endDateTime->diff($startDateTime);
        }

        if (null !== $interval && null === $endDateTime) {
            $endDateTime = new \DateTime();
            $endDateTime->add($endDateTime->diff($startDateTime));
            $endDateTime->add($interval);
        }

        $return = $arguments['return'];
        if (null === $return) {
            $spaceGlue = (boolean) $arguments['spaceGlue'];
            $glue = strval($arguments['glue']);
            $startFormat = $arguments['format'];
            $endFormat = $arguments['format'];
            if (null !== $arguments['startFormat'] && false === empty($arguments['startFormat'])) {
                $startFormat = $arguments['startFormat'];
            }
            if (null !== $arguments['endFormat'] && false === empty($arguments['endFormat'])) {
                $endFormat = $arguments['endFormat'];
            }
            $output = static::formatDate($startDateTime, $startFormat);
            $output .= true === $spaceGlue ? ' ' : '';
            $output .= $glue;
            $output .= true === $spaceGlue ? ' ' : '';
            $output .= static::formatDate($endDateTime, $endFormat);
        } elseif ('DateTime' === $return) {
            $output = $endDateTime;
        } elseif (true === is_string($return)) {
            if (false === strpos($return, '%')) {
                $return = '%' . $return;
            }
            $output = $interval->format($return);
        } elseif (true === is_array($return)) {
            $output = [];
            foreach ($return as $format) {
                if (false === strpos($format, '%')) {
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
     */
    protected static function enforceDateTime($date)
    {
        if (false === $date instanceof \DateTime) {
            try {
                if (true === is_integer($date)) {
                    $date = new \DateTime('@' . $date);
                } else {
                    $date = new \DateTime($date);
                }
                $date->setTimezone(new \DateTimeZone(date_default_timezone_get()));
            } catch (\Exception $exception) {
                ErrorUtility::throwViewHelperException('"' . $date . '" could not be parsed by \DateTime constructor.', 1369573112);
            }
        }
        return $date;
    }

    /**
     * @param \DateTime $date
     * @param string $format
     * @return string
     */
    protected static function formatDate($date, $format = 'Y-m-d')
    {
        if (false !== strpos($format, '%')) {
            return strftime($format, $date->format('U'));
        } else {
            return $date->format($format);
        }
    }
}
