<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Math: Minimum
 *
 * Gets the lowest number in array $a or the lowest
 * number of numbers $a and $b.
 */
class MinimumViewHelper extends AbstractMultipleMathViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->overrideArgument('b', 'mixed', 'Second number or Iterator/Traversable/Array for calculation');
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
        if (true === $aIsIterable && null === $b) {
            $a = $this->arrayFromArrayOrTraversableOrCSV($a);
            return min($a);
        }
        return $this->calculate($a, $b);
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @return mixed
     */
    protected function calculateAction($a, $b)
    {
        return min($a, $b);
    }
}
