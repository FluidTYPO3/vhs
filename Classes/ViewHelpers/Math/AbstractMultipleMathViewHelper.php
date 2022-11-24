<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;
use FluidTYPO3\Vhs\Utility\ErrorUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Base class: Math ViewHelpers operating on one number or an
 * array of numbers.
 */
abstract class AbstractMultipleMathViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;
    use ArrayConsumingViewHelperTrait;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('a', 'mixed', 'First number for calculation');
        $this->registerArgument('b', 'mixed', 'Second number or Iterator/Traversable/Array for calculation', true);
        $this->registerArgument(
            'fail',
            'boolean',
            'If TRUE, throws an Exception if argument "a" is not specified and no child content or inline argument ' .
            'is found. Usually okay to use a NULL value (as integer zero).',
            false,
            false
        );
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
     * @param numeric|array|iterable $a
     * @param numeric|array|iterable|null $b
     * @param array $arguments
     * @return numeric|array
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

    /**
     * @param numeric|array|iterable $a
     * @param numeric|array|iterable|null $b
     * @param array $arguments $b
     * @return numeric|array
     */
    abstract protected static function calculateAction($a, $b, array $arguments);
}
