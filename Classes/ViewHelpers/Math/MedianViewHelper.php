<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Math: Median
 *
 * Gets the median value from an array of numbers. If there
 * is an odd number of numbers the middle value is returned.
 * If there is an even number of numbers an average of the
 * two middle numbers is returned.
 */
class MedianViewHelper extends AbstractSingleMathViewHelper
{

    /**
     * @return mixed
     * @throw Exception
     */
    public function render()
    {
        $a = $this->getInlineArgument();
        $aIsIterable = $this->assertIsArrayOrIterator($a);
        if (true === $aIsIterable) {
            $a = $this->arrayFromArrayOrTraversableOrCSV($a);
            sort($a, SORT_NUMERIC);
            $size = count($a);
            $midpoint = $size / 2;
            if (1 === $size % 2) {
                /*
				 * Array indexes of float are truncated to integers,
				 * not everybody knows, let's make it explicit for everybody
				 * wondering.
				 */
                return $a[(integer) $midpoint];
            }
            $candidates = array_slice($a, floor($midpoint) - 1, 2);
            return array_sum($candidates) / 2;
        }
        return $a;
    }
}
