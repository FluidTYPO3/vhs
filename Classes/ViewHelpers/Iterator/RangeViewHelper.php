<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

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

    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerAsArgument();
        $this->registerArgument('low', 'integer', 'The low number of the range to be generated', false, 1);
        $this->registerArgument('high', 'integer', 'The high number of the range to be generated', true);
        $this->registerArgument('step', 'integer', 'The step (increment amount) between each number', false, 1);
    }

    /**
     * @return mixed
     */
    public function render()
    {
        return $this->renderChildrenWithVariableOrReturnInput(
            range($this->arguments['low'], $this->arguments['high'], $this->arguments['step'])
        );
    }
}
