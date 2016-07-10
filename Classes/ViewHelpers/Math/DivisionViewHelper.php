<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Math: Division
 *
 * Performs division of $a using $b. A can be an array and $b a
 * number, in which case each member of $a gets divided by $b.
 * If both $a and $b are arrays, each member of $a is summed
 * against the corresponding member in $b compared using index.
 */
class DivisionViewHelper extends AbstractMultipleMathViewHelper
{

    /**
     * @param mixed $a
     * @param mixed $b
     * @return mixed
     */
    protected function calculateAction($a, $b)
    {
        return (0 <> $b ? $a / $b : $a);
    }
}
