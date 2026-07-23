<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Condition\Iterator\ContainsViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Searches $haystack for index of $needle, returns -1 if $needle
 * is not in $haystack.
 */
class IndexOfViewHelper extends ContainsViewHelper
{
    public function render(): int
    {
        return (int) static::renderStatic(
            $this->arguments,
            $this->buildRenderChildrenClosure(),
            $this->renderingContext
        );
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): int {
        /** @var iterable $haystack */
        $haystack = $arguments['haystack'];
        $evaluation = static::assertHaystackHasNeedle($haystack, $arguments['needle'], $arguments);

        if (false !== $evaluation) {
            return (int) $evaluation;
        }
        return -1;
    }
}
