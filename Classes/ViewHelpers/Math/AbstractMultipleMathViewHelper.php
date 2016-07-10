<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * Base class: Math ViewHelpers operating on one number or an
 * array of numbers.
 */
abstract class AbstractMultipleMathViewHelper extends AbstractSingleMathViewHelper
{

    use ArrayConsumingViewHelperTrait;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('b', 'mixed', 'Second number or Iterator/Traversable/Array for calculation', true);
    }

    /**
     * @return mixed
     * @throw Exception
     */
    public function render()
    {
        $a = $this->getInlineArgument();
        $b = $this->arguments['b'];
        return $this->calculate($a, $b);
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @return mixed
     * @throws Exception
     */
    protected function calculate($a, $b = null)
    {
        if ($b === null) {
            throw new Exception('Required argument "b" was not supplied', 1237823699);
        }
        $aIsIterable = $this->assertIsArrayOrIterator($a);
        $bIsIterable = $this->assertIsArrayOrIterator($b);
        if (true === $aIsIterable) {
            $a = $this->arrayFromArrayOrTraversableOrCSV($a);
            foreach ($a as $index => $value) {
                $bSideValue = true === $bIsIterable ? $b[$index] : $b;
                $a[$index] = $this->calculateAction($value, $bSideValue);
            }
            return $a;
        } elseif (true === $bIsIterable) {
            // condition matched if $a is not iterable but $b is.
            throw new Exception(
                'Math operation attempted using an iterator $b against a numeric value $a. Either both $a and $b, ' .
                'or only $a, must be array/Iterator',
                1351890876
            );
        }
        return $this->calculateAction($a, $b);
    }
}
