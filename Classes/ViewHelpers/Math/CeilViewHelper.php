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
 * Math: Ceil
 *
 * Ceiling on $a which can be either an array-accessible
 * value (Iterator+ArrayAccess || array) or a raw numeric
 * value.
 */
class CeilViewHelper extends AbstractSingleMathViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @param mixed $a
     * @return integer|array
     */
    protected static function calculateAction($a, array $arguments = [])
    {
        if (static::assertIsArrayOrIterator($a)) {
            return array_map('ceil', static::arrayFromArrayOrTraversableOrCSVStatic($a));
        }
        /** @var integer|float $a */
        return (integer) ceil($a);
    }
}
