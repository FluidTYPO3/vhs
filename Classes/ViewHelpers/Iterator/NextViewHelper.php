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
 * Returns next element in array $haystack from position of $needle.
 */
class NextViewHelper extends ContainsViewHelper
{
    public function render(): mixed
    {
        return static::renderStatic($this->arguments, $this->buildRenderChildrenClosure(), $this->renderingContext);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): mixed {
        /** @var iterable $haystack */
        $haystack = $arguments['haystack'];
        $evaluation = static::assertHaystackHasNeedle(
            $haystack,
            $arguments['needle'] ?? $renderChildrenClosure(),
            $arguments
        );
        return static::getNeedleAtIndex($evaluation !== false ? $evaluation + 1 : -1, $arguments);
    }
}
