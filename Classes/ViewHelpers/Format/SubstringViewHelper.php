<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Gets a substring from a string or string-compatible value.
 *
 * Also see the `<f:format.crop>` view helper.
 */
class SubstringViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('content', 'string', 'Content string to substring');
        $this->registerArgument('start', 'integer', 'Positive or negative offset', false, 0);
        $this->registerArgument('length', 'integer', 'Positive or negative length');
    }

    /**
     * Substrings a string or string-compatible value
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
        $content = $renderChildrenClosure();
        $start = (integer) $arguments['start'];
        $length = $arguments['length'];
        if (null !== $length) {
            if ($length < 0) {
                // mb_substr does not support negative length, therefore we must calculate the length based on
                // original string length and offset, so we can pass a positive integer to mb_substr.
                $length = $length + mb_strlen($content) - $start;
            }
            return mb_substr($content, $start, $length);
        }
        return mb_substr($content, $start);
    }
}
