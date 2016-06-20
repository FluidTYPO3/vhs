<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

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

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->overrideArgument('b', 'mixed', 'Optional: Second number or Iterator/Traversable/Array for calculation');
    }

    /**
     * @return mixed
     * @throw Exception
     */
    public function render()
    {
        $a = $this->getInlineArgument();
        $b = $this->arguments['b'];
        $aIsIterable = $this->assertIsArrayOrIterator($a);
        $bIsIterable = $this->assertIsArrayOrIterator($b);
        if (true === $aIsIterable && null === $b) {
            $a = $this->arrayFromArrayOrTraversableOrCSV($a);
            $sum = array_sum($a);
            $distribution = count($a);
            return $sum / $distribution;
        } elseif (true === $aIsIterable && false === $bIsIterable) {
            $a = $this->arrayFromArrayOrTraversableOrCSV($a);
            foreach ($a as $index => $value) {
                $a[$index] = $this->calculateAction($value, $b);
            }
            return $a;
        } elseif (true === isset($a) && null === $b) {
            return $a;
        }
        return $this->calculate($a, $b);
    }

    /**
     * @param mixed $a
     * @param $b
     * @return mixed
     */
    protected function calculateAction($a, $b)
    {
        return ($a + $b) / 2;
    }
}
