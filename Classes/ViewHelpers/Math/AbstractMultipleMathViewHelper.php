<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ErrorUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * Base class: Math ViewHelpers operating on one number or an
 * array of numbers.
 */
abstract class AbstractMultipleMathViewHelper extends AbstractSingleMathViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('b', 'mixed', 'Second number or Iterator/Traversable/Array for calculation', true);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $value = $renderChildrenClosure();
        if (null === $value && true === (boolean) $arguments['fail']) {
            ErrorUtility::throwViewHelperException('Required argument "a" was not supplied', 1237823699);
        }
        return static::calculate($value, $arguments['b'], $arguments);
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @param array $arguments
     * @return mixed
     * @throws Exception
     */
    protected static function calculate($a, $b = null, array $arguments = [])
    {
        $aIsIterable = static::assertIsArrayOrIterator($a);
        $bIsIterable = static::assertIsArrayOrIterator($b);
        if (false === $aIsIterable && true === $bIsIterable) {
            // condition matched if $a is not iterable but $b is.
            ErrorUtility::throwViewHelperException(
                'Math operation attempted using an iterator $b against a numeric value $a. Either both $a and $b, ' .
                'or only $a, must be array/Iterator',
                1351890876
            );
        }
        return static::calculateAction($a, $b, $arguments);
    }
}
