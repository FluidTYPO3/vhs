<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Unless
 *
 * The opposite of `f:if` and only supporting negative matching.
 * Related to `v:or` but allows more complex conditions.
 *
 * Is the same as writing:
 *
 *     <f:if condition="{theThingToCheck}">
 *         <f:else>
 *             The thing that gets done
 *         </f:else>
 *     </f:if>
 *
 * Except without the `f:else`.
 *
 * #### Example, tag mode
 *
 *     <v:unless condition="{somethingRequired}">
 *         Warning! Something required was not present.
 *     </v:unless>
 *
 * #### Example, inline mode illustrating `v:or` likeness
 *
 *     {defaultText -> v:unless(condition: originalText)}
 *         // which is much the same as...
 *     {originalText -> v:or(alternative: defaultText}
 *         // ...but the "unless" counterpart supports anything as
 *         // condition instead of only checking "is content empty?"
 *
 * @package Vhs
 * @subpackage ViewHelpers
 */
class UnlessViewHelper extends AbstractConditionViewHelper
{
    /**
     * Rendering with inversion and ignoring any f:then / f:else children.
     *
     * @return string|NULL
     */
    public function render()
    {
        if (!static::evaluateCondition($this->arguments)) {
            return $this->renderChildren();
        }
        return null;
    }

    /**
     * Static rendering with inversion and ignoring any f:then / f:else children.
     *
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
        if (!static::evaluateCondition($arguments)) {
            return $renderChildrenClosure();
        }
        return null;
    }
}
