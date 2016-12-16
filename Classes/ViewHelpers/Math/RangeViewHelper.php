<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Math: Range
 *
 * Gets the lowest and highest number from an array of numbers.
 * Returns an array of [low, high]. For individual low/high
 * values please use v:math.maximum and v:math.minimum.
 */
class RangeViewHelper extends AbstractSingleMathViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;
    use ArrayConsumingViewHelperTrait;

    /**
     * @param mixed $a
     * @return mixed
     * @throw Exception
     */
    protected static function calculateAction($a)
    {
        return [min($a), max($a)];
    }
}
