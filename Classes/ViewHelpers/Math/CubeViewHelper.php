<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Math: Square
 *
 * Performs $a ^ 3.
 */
class CubeViewHelper extends AbstractSingleMathViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @param integer|float|string|array|iterable $a
     * @return float|array
     */
    protected static function calculateAction($a, array $arguments = [])
    {
        if (static::assertIsArrayOrIterator($a)) {
            return array_map([static::class, 'calculateAction'], static::arrayFromArrayOrTraversableOrCSVStatic($a));
        }
        /** @var integer|float $a */
        return pow($a, 3);
    }
}
