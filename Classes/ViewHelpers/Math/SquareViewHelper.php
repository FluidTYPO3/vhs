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
 * Math: Square
 *
 * Performs $a ^ 2.
 */
class SquareViewHelper extends AbstractSingleMathViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @param mixed $a
     * @return integer
     */
    protected static function calculateAction($a)
    {
        if (static::assertIsArrayOrIterator($a)) {
            return array_map(
                [static::class, 'calculateAction'],
                static::arrayFromArrayOrTraversableOrCSVStatic($a)
            );
        }
        return pow($a, 2);
    }
}
