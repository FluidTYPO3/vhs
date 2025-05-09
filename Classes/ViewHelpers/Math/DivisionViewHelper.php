<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\CompileWithContentArgumentAndRenderStatic;
use FluidTYPO3\Vhs\Utility\ErrorUtility;

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
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @param mixed $a
     * @param mixed $b
     * @param array $arguments
     * @return mixed
     */
    protected static function calculateAction($a, $b, array $arguments)
    {
        $aIsIterable = static::assertIsArrayOrIterator($a);
        $bIsIterable = static::assertIsArrayOrIterator($b);
        if (!$aIsIterable && $b === null && $arguments['fail']) {
            ErrorUtility::throwViewHelperException('Required argument "b" was not supplied', 1237823699);
        }
        if ($aIsIterable && null === $b) {
            $a = static::arrayFromArrayOrTraversableOrCSVStatic($a);
            $sum = array_sum($a);
            $distribution = count($a);
            return $sum / $distribution;
        } elseif ($aIsIterable && $b !== null) {
            $a = static::arrayFromArrayOrTraversableOrCSVStatic($a);
            if ($bIsIterable) {
                $b = static::arrayFromArrayOrTraversableOrCSVStatic($b);
            }
            foreach ($a as $index => $value) {
                $bSide = is_array($b) ? $b[$index] : $b;
                $a[$index] = static::calculateAction($value, $bSide, $arguments);
            }
            return $a;
        }
        return (0 <> $b ? $a / $b : $a);
    }
}
