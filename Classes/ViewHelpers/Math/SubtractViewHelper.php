<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArgumentOverride;
use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;
use FluidTYPO3\Vhs\Traits\CompileWithContentArgumentAndRenderStatic;
use FluidTYPO3\Vhs\Utility\ErrorUtility;

/**
 * Math: Subtract
 *
 * Performs subtraction of $a and $b. A can be an array and $b a
 * number, in which case each member of $a gets subtracted $b.
 * If $a is an array and $b is not provided then neg. array_sum is
 * used to return a single numeric value. If both $a and $b are
 * arrays, each member of $a is summed against the corresponding
 * member in $b compared using index.
 */
class SubtractViewHelper extends AbstractMultipleMathViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;
    use ArrayConsumingViewHelperTrait;
    use ArgumentOverride;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->overrideArgument('b', 'mixed', 'Optional: Second number or Iterator/Traversable/Array for calculation');
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @return mixed
     */
    protected static function calculateAction($a, $b, array $arguments)
    {
        $aIsIterable = static::assertIsArrayOrIterator($a);
        if (!$aIsIterable && $b === null && $arguments['fail']) {
            ErrorUtility::throwViewHelperException('Required argument "b" was not supplied', 1237823699);
        }
        if ($aIsIterable && null === $b) {
            $a = static::arrayFromArrayOrTraversableOrCSVStatic($a);
            return -array_sum($a);
        }
        return $a - $b;
    }
}
