<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Math: SquareRoot
 *
 * Performs sqrt($a).
 */
class SquareRootViewHelper extends AbstractSingleMathViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @param integer|float|string|array $a
     * @return float|array
     */
    protected static function calculateAction($a, array $arguments = [])
    {
        if (static::assertIsArrayOrIterator($a)) {
            return array_map('sqrt', static::arrayFromArrayOrTraversableOrCSVStatic($a));
        }
        if (!is_scalar($a)) {
            return 0;
        }
        return sqrt((float) $a);
    }
}
