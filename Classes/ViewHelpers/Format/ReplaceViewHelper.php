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
 */
class ReplaceViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    public function initializeArguments(): void
    {
        $this->registerArgument('content', 'string', 'Content in which to perform replacement');
        $this->registerArgument('substring', 'string', 'Substring to replace', true);
        $this->registerArgument('replacement', 'string', 'Replacement to insert', false, '');
        $this->registerArgument(
            'returnCount',
            'bool',
            'If TRUE, returns the number of replacements that were performed instead of returning output string. ' .
            'See also `v:count.substring`.'
        );
        $this->registerArgument('caseSensitive', 'boolean', 'If true, perform case-sensitive replacement', false, true);
    }

    /**
     * @return string|int
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $content = (string) $renderChildrenClosure();
        /** @var string $substring */
        $substring = (string) $arguments['substring'];
        /** @var string $replacement */
        $replacement = (string) $arguments['replacement'];
        /** @var int|null $count */
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
