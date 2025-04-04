<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\CompileWithRenderStatic;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Iterator Range ViewHelper
 *
 * Implementation of `range` for Fluid
 *
 * Creates a new array of numbers from the low to the high given
 * value, incremented by the step value.
 *
 * #### Usage examples
 *
 * ```xml
 * Numbers 1-10: {v:iterator.implode(glue: ',') -> v:iterator.range(low: 1, high: 10)}
 * Even numbers 0-10: {v:iterator.implode(glue: ',') -> v:iterator.range(low: 0, high: 10, step: 2)}
 * ```
 */
class RangeViewHelper extends AbstractViewHelper
{
    use TemplateVariableViewHelperTrait;
    use CompileWithRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('low', 'integer', 'The low number of the range to be generated', false, 1);
        $this->registerArgument('high', 'integer', 'The high number of the range to be generated', true);
        $this->registerArgument('step', 'integer', 'The step (increment amount) between each number', false, 1);
        $this->registerAsArgument();
    }

    /**
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        /** @var int $low */
        $low = $arguments['low'];
        /** @var int $high */
        $high = $arguments['high'];
        /** @var int $step */
        $step = $arguments['step'];
        /** @var string|null $as */
        $as = $arguments['as'];
        return static::renderChildrenWithVariableOrReturnInputStatic(
            range($low, $high, $step),
            $as,
            $renderingContext,
            $renderChildrenClosure
        );
    }
}
