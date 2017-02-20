<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Condition\Iterator\ContainsViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Searches $haystack for index of $needle, returns -1 if $needle
 * is not in $haystack.
 */
class IndexOfViewHelper extends ContainsViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Default implementation for use in compiled templates
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
        $evaluation = self::assertHaystackHasNeedle($arguments['haystack'], $arguments['needle'], $arguments);

        if (false !== $evaluation) {
            return (integer) $evaluation;
        }
        return -1;
    }
}
