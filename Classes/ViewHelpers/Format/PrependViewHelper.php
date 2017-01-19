<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * ### Format: Prepend string content
 *
 * Prepends one string on another. Although this task is very
 * easily done in standard Fluid - i.e. {add}{subject} - this
 * ViewHelper makes advanced chained inline processing possible:
 *
 *     <!-- Adds 1H to DateTime, formats using timestamp input which requires prepended @ -->
 *     {dateTime.timestamp
 *         -> v:math.sum(b: 3600)
 *         -> v:format.prepend(add: '@')
 *         -> v:format.date(format: 'Y-m-d H:i')}
 *     <!-- You don't have to break the syntax into lines; done here for display only -->
 */
class PrependViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('subject', 'string', 'String to prepend other string to');
        $this->registerArgument('add', 'string', 'String to prepend');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        return $arguments['add'] . $renderChildrenClosure();
    }
}
