<?php
namespace FluidTYPO3\Vhs\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * This trait can be used by viewhelpers that generate image tags
 * to add srcsets based to the imagetag for better responsiveness
 */
trait ConditionViewHelperTrait
{

    /**
     * renders <f:then> child if $condition is true, otherwise renders <f:else> child.
     *
     * @return string the rendered string
     * @api
     */
    public function render()
    {
        if (static::evaluateCondition($this->arguments)) {
            return $this->renderThenChild();
        } else {
            return $this->renderElseChild();
        }
    }

    /**
     * Default implementation for use in compiled templates
     *
     * TODO: remove at some point, because this is only here for legacy reasons.
     * the AbstractConditionViewHelper in 6.2.* doesn't have a default render
     * method. 7.2+ on the other hand provides basically exactly this method here
     * luckily it's backwards compatible out of the box.
     * tl;dr -> remove after expiration of support for anything below 7.2
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param \TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
     * @return mixed
     */
    static public function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        \TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
    ) {
        $hasEvaluated = true;
        if (static::evaluateCondition($arguments)) {
            $result = static::renderStaticThenChild($arguments, $hasEvaluated);
            if ($hasEvaluated) {
                return $result;
            }

            return $renderChildrenClosure();
        } else {
            $result = static::renderStaticElseChild($arguments, $hasEvaluated);
            if ($hasEvaluated) {
                return $result;
            }
        }

        return '';
    }

    /**
     * Statically evalute "then" children.
     * The "$hasEvaluated" argument is there to distinguish the case that "then" returned NULL or was not evaluated.
     *
     * TODO: remove at some point, because this is only here for legacy reasons.
     * the AbstractConditionViewHelper in 6.2.* doesn't have a default render
     * method. 7.2+ on the other hand provides basically exactly this method here
     * luckily it's backwards compatible out of the box.
     * tl;dr -> remove after expiration of support for anything below 7.2
     *
     * @param array $arguments ViewHelper arguments
     * @param bool $hasEvaluated Can be used to check if the "then" child was actually evaluated by this method.
     * @return string
     */
    static protected function renderStaticThenChild($arguments, &$hasEvaluated)
    {
        if (isset($arguments['then'])) {
            return $arguments['then'];
        }
        if (isset($arguments['__thenClosure'])) {
            $thenClosure = $arguments['__thenClosure'];
            return $thenClosure();
        } elseif (isset($arguments['__elseClosure'])) {
            return '';
        }

        $hasEvaluated = false;
    }

    /**
     * Statically evalute "else" children.
     * The "$hasEvaluated" argument is there to distinguish the case that "else" returned NULL or was not evaluated.
     *
     * TODO: remove at some point, because this is only here for legacy reasons.
     * the AbstractConditionViewHelper in 6.2.* doesn't have a default render
     * method. 7.2+ on the other hand provides basically exactly this method here
     * luckily it's backwards compatible out of the box.
     * tl;dr -> remove after expiration of support for anything below 7.2
     *
     * @param array $arguments ViewHelper arguments
     * @param bool $hasEvaluated Can be used to check if the "else" child was actually evaluated by this method.
     * @return string
     */
    static protected function renderStaticElseChild($arguments, &$hasEvaluated)
    {
        if (isset($arguments['else'])) {
            return $arguments['else'];
        }
        if (isset($arguments['__elseClosure'])) {
            $elseClosure = $arguments['__elseClosure'];
            return $elseClosure();
        }

        $hasEvaluated = false;
    }

    /**
     * This method decides if the condition is TRUE or FALSE.
     *
     * TODO: remove at some point, because this is only here for legacy reasons.
     * the AbstractConditionViewHelper in 6.2.* doesn't have a default render
     * method. 7.2+ on the other hand provides basically exactly this method here
     * luckily it's backwards compatible out of the box.
     * tl;dr -> remove after expiration of support for anything below 7.2
     *
     * @param array $arguments
     * @return bool
     */
    static protected function evaluateCondition($arguments = null)
    {
        return (isset($arguments['condition']) && $arguments['condition']);
    }
}
