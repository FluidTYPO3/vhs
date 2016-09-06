<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Math: Range
 *
 * Gets the lowest and highest number from an array of numbers.
 * Returns an array of [low, high]. For individual low/high
 * values please use v:math.maximum and v:math.minimum.
 */
class RangeViewHelper extends AbstractSingleMathViewHelper
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
            if (1 === count($a)) {
                return [reset($a), reset($a)];
            } else {
                return [array_shift($a), array_pop($a)];
            }
        }
        return $a;
    }
}
