<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ViewHelperUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * ### Variable: Unset
 *
 * Quite simply, removes a currently available variable
 * from the TemplateVariableContainer:
 *
 *     <!-- Data: {person: {name: 'Elvis', nick: 'King'}} -->
 *     I'm {person.name}. Call me "{person.nick}". A ding-dang doo!
 *     <v:variable.unset name="person" />
 *     <f:if condition="{person}">
 *         <f:else>
 *             You saw this coming...
 *             <em>Elvis has left the building</em>
 *         </f:else>
 *     </f:if>
 *
 * At the time of writing this, `v:variable.unset` is not able
 * to remove members of for example arrays:
 *
 *     <!-- DOES NOT WORK! -->
 *     <v:variable.unset name="myObject.propertyName" />
 */
class UnsetViewHelper extends AbstractViewHelper implements CompilableInterface
{
    use CompileWithRenderStatic;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('name', 'string', 'Name of variable in variable container', true);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return void
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $name = $arguments['name'];
        $variableProvider = ViewHelperUtility::getVariableProviderFromRenderingContext($renderingContext);
        if ($variableProvider->exists($name)) {
            $variableProvider->remove($name);
        }
    }
}
