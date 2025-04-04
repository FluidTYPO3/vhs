<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Count;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\CompileWithContentArgumentAndRenderStatic;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Counts number of lines in a string.
 *
 * #### Usage examples
 *
 * ```
 * <v:count.substring string="{myString}">{haystack}</v:count.substring> (output for example `2`
 * ```
 *
 * ```
 * {haystack -> v:count.substring(string: myString)} when used inline
 * ```
 *
 * ```
 * <v:count.substring string="{myString}" haystack="{haystack}" />
 * ```
 *
 * ```
 * {v:count.substring(string: myString, haystack: haystack)}
 * ```
 */
class SubstringViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('haystack', 'string', 'String to count substring in, if not provided as tag content');
        $this->registerArgument('string', 'string', 'Substring to count occurrences of', true);
    }

    /**
     * @return integer
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        /** @var string $string */
        $string = $arguments['string'];
        return mb_substr_count(
            $renderChildrenClosure(),
            $string
        );
    }
}
