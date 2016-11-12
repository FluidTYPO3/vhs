<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Count;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Counts words in a string.
 *
 * #### Usage examples
 *
 *     <v:count.words>{myString}</v:count.words> (output for example `42`
 *
 *     {myString -> v:count.words()} when used inline
 *
 *     <v:count.words string="{myString}" />
 *
 *     {v:count.words(string: myString)}
 */
class WordsViewHelper extends AbstractViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('string', 'string', 'String to count, if not provided as tag content');
    }

    /**
     * @return integer
     */
    public function render()
    {
        return static::renderStatic(
            $this->arguments,
            $this->buildRenderChildrenClosure(),
            $this->renderingContext
        );
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return integer
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        return count(
            preg_split(
                '~[^\p{L}\p{N}\']+~u',
                strip_tags(
                    str_replace(
                        '><',
                        '> <',
                        isset($arguments['string']) ? $arguments['string'] : $renderChildrenClosure()
                    )
                )
            )
        );
    }
}
