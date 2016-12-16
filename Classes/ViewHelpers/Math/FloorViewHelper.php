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
 * Math: Floor
 *
 * Floors $a which can be either an array-accessible
 * value (Iterator+ArrayAccess || array) or a raw numeric
 * value.
 */
class FloorViewHelper extends AbstractSingleMathViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @param mixed $a
     * @return integer
     */
    protected static function calculateAction($a)
    {
        if (static::assertIsArrayOrIterator($a)) {
            return array_map('floor', static::arrayFromArrayOrTraversableOrCSVStatic($a));
        }
        return floor($a);
    }
}
