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
 * <v:count.lines>{myString}</v:count.lines> (output for example `42`
 * ```
 *
 * ```
 * {myString -> v:count.lines()} when used inline
 * ```
 *
 * ```
 * <v:count.lines string="{myString}" />
 * ```
 *
 * ```
 * {v:count.lines(string: myString)}
 * ```
 */
class LinesViewHelper extends AbstractViewHelper
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
        $value = $renderChildrenClosure();
        if ((string) $value === '') {
            return 0;
        }
        return mb_substr_count($value, PHP_EOL) + 1;
    }
}
