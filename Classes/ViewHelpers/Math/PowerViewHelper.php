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
 * Math: Power
 *
 * Performs pow($a, $b) where $a is the base and $b is the exponent.
 */
class PowerViewHelper extends AbstractMultipleMathViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @param mixed $a
     * @param mixed $b
     * @return integer
     */
    protected static function calculateAction($a, $b)
    {
        return pow($a, $b);
    }
}
