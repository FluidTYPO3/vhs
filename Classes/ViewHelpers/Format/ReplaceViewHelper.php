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
 * Replaces $substring in $content with $replacement.
 *
 * Supports array as input substring/replacements and content.
 *
 * When input substring/replacement is an array, both must be
 * the same length and must contain only strings.
 *
 * When input content is an array, the search/replace is done
 * on every value in the input content array and the return
 * value will be an array of equal size as the input content
 * array but with all values search/replaced. All values in the
 * input content array must be strings.
 */
class ReplaceViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    public function initializeArguments(): void
    {
        $this->registerArgument('content', 'string', 'Content in which to perform replacement. Array supported.');
        $this->registerArgument('substring', 'string', 'Substring to replace. Array supported.', true);
        $this->registerArgument('replacement', 'string', 'Replacement to insert. Array supported.', false, '');
        $this->registerArgument(
            'returnCount',
            'bool',
            'If TRUE, returns the number of replacements that were performed instead of returning output string. ' .
            'See also `v:count.substring`.'
        );
        $this->registerArgument('caseSensitive', 'boolean', 'If true, perform case-sensitive replacement', false, true);
    }

    /**
     * @return array|string|int
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $content = $renderChildrenClosure();
        /** @var string|array $content */
        $content = is_scalar($content) || $content === null ? (string) $content : (array) $content;

        $substring = $arguments['substring'];
        /** @var string|array $substring */
        $substring = is_scalar($substring) ? (string) $substring : (array) $substring;

        $replacement = $arguments['replacement'];
        /** @var string|array $replacement */
        $replacement = is_scalar($replacement) ? (string) $replacement : (array) $replacement;

        $count = 0;
        $caseSensitive = (boolean) $arguments['caseSensitive'];
        $function = $caseSensitive ? 'str_replace' : 'str_ireplace';
        $replaced = $function($substring, $replacement, $content, $count);
        if ($arguments['returnCount'] ?? false) {
            return $count;
        }
        return $replaced;
    }
}
