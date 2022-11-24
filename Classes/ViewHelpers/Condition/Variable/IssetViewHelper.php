<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

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
 * ```
 * <!-- if {variableContainingVariableName} is "foo" this checks existence of {foo} -->
 * <v:condition.variable.isset name="{variableContainingVariableName}">...</v:condition.variable.isset>
 * <!-- if {suffix} is "Name" this checks existence of "variableName" -->
 * <v:condition.variable.isset name="variable{suffix}">...</v:condition.variable.isset>
 * <!-- outputs value of {foo} if {bar} is defined -->
 * {foo -> v:condition.variable.isset(name: bar)}
 * ```
 *
 * ONLY WORKS ON TYPO3v8+!
 */
class IssetViewHelper extends AbstractConditionViewHelper
{
    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('name', 'string', 'name of the variable', true);
    }

    public static function verdict(array $arguments, RenderingContextInterface $renderingContext)
    {
        return $renderingContext->getVariableProvider()->exists($arguments['name']);
    }
}
