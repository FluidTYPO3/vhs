<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Math: Average
 *
 * Performs average across an array. If $a is an array and
 * $b is an array, each member of $a is averaged against the
 * same member in $b. If $a is an array and $b is a number,
 * each member of $a is averaged agained $b. If $a is an array
 * this array is averaged to one number. If $a is a number and
 * $b is not provided or NULL, $a is gracefully returned as an
 * average value of itself.
 */
class AverageViewHelper extends AbstractMultipleMathViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;
    use ArrayConsumingViewHelperTrait;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->overrideArgument('b', 'mixed', 'Optional: Second number or Iterator/Traversable/Array for calculation');
    }

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
        if (true === $aIsIterable) {
            $a = static::arrayFromArrayOrTraversableOrCSVStatic($a);
            if ($b === null) {
                return array_sum($a) / count($a);
            }
            if ($bIsIterable) {
                $b = static::arrayFromArrayOrTraversableOrCSVStatic($b);
            }
            foreach ($a as $index => $value) {
                $bSide = $bIsIterable ? $b[$index] : $b;
                $a[$index] = static::calculateAction($value, $bSide, $arguments);
            }
            return $a;
        }
        if (null === $b) {
            return $a;
        }
        return ($a + $b) / 2;
    }
}
