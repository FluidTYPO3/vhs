<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Random;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Random: Number Generator
 *
 * Generates a random number. The default minimum number is
 * set to 100000 in order to generate a longer integer string
 * representation. Decimal values can be generated as well.
 */
class NumberViewHelper extends AbstractViewHelper
{

    /**
     * @param integer $minimum Minimum number - defaults to 100000 (default max is 999999 for equal string lengths)
     * @param integer $maximum Maximum number - defaults to 999999 (default min is 100000 for equal string lengths)
     * @param integer $minimumDecimals Minimum number of also randomized decimal digits to add to number
     * @param integer $maximumDecimals Maximum number of also randomized decimal digits to add to number
     * @return float
     */
    public function render($minimum = 100000, $maximum = 999999, $minimumDecimals = 0, $maximumDecimals = 0)
    {
        $natural = rand($minimum, $maximum);
        if (0 === (integer) $minimumDecimals && 0 === (integer) $maximumDecimals) {
            return $natural;
        }
        $decimals = array_fill(0, rand($minimumDecimals, $maximumDecimals), 0);
        $decimals = array_map(function () {
            return rand(0, 9);
        }, $decimals);
        return floatval($natural . '.' . implode('', $decimals));
    }
}
