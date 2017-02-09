<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Variable: Isset
 *
 * Renders the `then` child if the variable name given in
 * the `name` argument exists in the template. The value
 * can be zero, NULL or an empty string - but the ViewHelper
 * will still return TRUE if the variable exists.
 *
 * Combines well with dynamic variable names:
 *
 *     <!-- if {variableContainingVariableName} is "foo" this checks existence of {foo} -->
 *     <v:condition.variable.isset name="{variableContainingVariableName}">...</v:condition.variable.isset>
 *     <!-- if {suffix} is "Name" this checks existence of "variableName" -->
 *     <v:condition.variable.isset name="variable{suffix}">...</v:condition.variable.isset>
 *     <!-- outputs value of {foo} if {bar} is defined -->
 *     {foo -> v:condition.variable.isset(name: bar)}
 */
class IssetViewHelper extends AbstractConditionViewHelper
{
    /**
     * @var RenderingContextInterface
     */
    protected static $storedRenderingContext;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('name', 'string', 'name of the variable', true);
    }
    /**
     * Render
     *
     * @return string
     */
    public function render()
    {
        return static::renderStatic(
            $this->arguments,
            $this->buildRenderChildrenClosure(),
            $this->renderingContext
        );
    }

    /**
     * @param array $arguments
     * @return bool
     */
    protected static function evaluateCondition($arguments = null)
    {
        return (true === static::$storedRenderingContext->getTemplateVariableContainer()->exists($arguments['name']));
    }

    /**
     * Default implementation for use in compiled templates
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param \TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        static::$storedRenderingContext = $renderingContext;
        $result = parent::renderStatic($arguments, $renderChildrenClosure, $renderingContext);
        static::$storedRenderingContext = null;
        return $result;
    }
}
