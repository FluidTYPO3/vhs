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
 * Counts bytes (multibyte-safe) in a string.
 *
 * #### Usage examples
 *
 *     <v:count.bytes>{myString}</v:count.bytes> (output for example `42`
 *
 *     {myString -> v:count.bytes()} when used inline
 *
 *     <v:count.bytes string="{myString}" />
 *
 *     {v:count.bytes(string: myString)}
 */
class BytesViewHelper extends AbstractViewHelper
{
    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('string', 'string', 'String to count, if not provided as tag content');
        $this->registerArgument('encoding', 'string', 'Character set encoding of string, e.g. UTF-8 or ISO-8859-1', false, 'UTF-8');
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
        return mb_strlen(isset($arguments['string']) ? $arguments['string'] : $renderChildrenClosure(), $arguments['encoding']);
    }
}
