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
abstract class AbstractSingleMathViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;
    use ArrayConsumingViewHelperTrait;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('a', 'mixed', 'First number for calculation');
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
     * @throws Exception
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
        return static::calculateAction($value, $arguments);
    }

    /**
     * @param numeric|array|iterable $a
     * @return numeric|array
     */
    abstract protected static function calculateAction($a, array $arguments = []);
}
