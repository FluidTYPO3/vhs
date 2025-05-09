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
 * Counts words in a string.
 *
 * #### Usage examples
 *
 * ```
 * <v:count.words>{myString}</v:count.words> (output for example `42`
 * ```
 *
 * ```
 * {myString -> v:count.words()} when used inline
 * ```
 *
 * ```
 * <v:count.words string="{myString}" />
 * ```
 *
 * ```
 * {v:count.words(string: myString)}
 * ```
 */
class WordsViewHelper extends AbstractViewHelper
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
        $this->registerArgument('string', 'string', 'String to count, if not provided as tag content');
    }

    /**
     * @return integer
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        return count(
            (array) preg_split(
                '~[^\p{L}\p{N}\']+~u',
                strip_tags(
                    str_replace(
                        '><',
                        '> <',
                        $renderChildrenClosure()
                    )
                )
            )
        );
    }
}
