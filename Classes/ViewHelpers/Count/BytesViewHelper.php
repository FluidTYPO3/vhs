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
 * Counts bytes (multibyte-safe) in a string.
 *
 * #### Usage examples
 *
 * ```
 * <v:count.bytes>{myString}</v:count.bytes> (output for example `42`
 * ```
 *
 * ```
 * {myString -> v:count.bytes()} when used inline
 * ```
 *
 * ```
 * <v:count.bytes string="{myString}" />
 * ```
 *
 * ```
 * {v:count.bytes(string: myString)}
 * ```
 */
class BytesViewHelper extends AbstractViewHelper
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
        $this->registerArgument(
            'encoding',
            'string',
            'Character set encoding of string, e.g. UTF-8 or ISO-8859-1',
            false,
            'UTF-8'
        );
    }

    /**
     * @return integer
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        /** @var string $encoding */
        $encoding = $arguments['encoding'];
        return (integer) mb_strlen($renderChildrenClosure(), $encoding);
    }
}
