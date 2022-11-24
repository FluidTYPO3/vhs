<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Adds one variable to the end of the array and returns the result.
 *
 * Example:
 *
 * ```
 * <f:for each="{array -> v:iterator.push(add: additionalObject, key: 'newkey')}" as="combined">
 * ...
 * </f:for>
 * ```
 */
class PushViewHelper extends AbstractViewHelper
{
    use TemplateVariableViewHelperTrait;
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
        $this->registerArgument('subject', 'mixed', 'Input to work on - Array/Traversable/...');
        $this->registerArgument('add', 'mixed', 'Member to add to end of array', true);
        $this->registerArgument('key', 'mixed', 'Optional key to use. If key exists the member will be overwritten!');
        $this->registerAsArgument();
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
        $subject = empty($arguments['as'])
            ? ($arguments['subject'] ?? $renderChildrenClosure())
            : $arguments['subject'];
        $add = $arguments['add'];
        $key = $arguments['key'];
        if ($key) {
            $subject[$key] = $add;
        } else {
            $subject[] = $add;
        }
        return static::renderChildrenWithVariableOrReturnInputStatic(
            $subject,
            $arguments['as'],
            $renderingContext,
            $renderChildrenClosure
        );
    }
}
