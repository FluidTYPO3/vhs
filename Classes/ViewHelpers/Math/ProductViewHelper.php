<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * ### Math: Product (multiplication)
 *
 * Product (multiplication) of $a and $b. A can be an array and $b a
 * number, in which case each member of $a gets multiplied by $b.
 * If $a is an array and $b is not provided then array_product is
 * used to return a single numeric value. If both $a and $b are
 * arrays, each member of $a is multiplied against the corresponding
 * member in $b compared using index.
 */
class ProductViewHelper extends AbstractMultipleMathViewHelper
{

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
            return array_product($a);
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
        return $a * $b;
    }
}
