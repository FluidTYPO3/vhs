<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Math: CubicRoot
 *
 * Performs pow($a, 1/3) - cubic or third root.
 */
class CubicRootViewHelper extends AbstractSingleMathViewHelper
{

    /**
     * @param mixed $a
     * @return integer
     */
    protected function calculateAction($a)
    {
        return pow($a, 1 / 3);
    }
}
