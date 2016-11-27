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
 * Math: Modulo
 * Perform modulo on $input. Returns the same type as $input,
 * i.e. if given an array, will transform each member and return
 * the result. Supports array and Iterator (in the following
 * descriptions "array" means both these types):
 *
 * If $a and $b are both arrays of the same size then modulo is
 * performed on $a using members of $b, by their index (so these
 * must match in both arrays).
 *
 * If $a is an array and $b is a number then modulo is performed
 * on $a using $b for each calculation.
 *
 * If $a and $b are both numbers simple modulo is performed.
 */
class ModuloViewHelper extends AbstractMultipleMathViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @param mixed $a
     * @param mixed $b
     * @return integer
     */
    protected static function calculateAction($a, $b)
    {
        return $a % $b;
    }
}
