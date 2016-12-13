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
 * Processes output as plaintext. Will trim whitespace off
 * each line that is provided, making display in a <pre>
 * work correctly indented even if the source is not.
 *
 * Expects that you use f:format.htmlentities or similar
 * if you do not want HTML to be displayed as HTML, or
 * simply want it stripped out.
 */
class PlaintextViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('content', 'string', 'Content to trim each line of text within');
    }

    /**
     * Trims content, then trims each line of content
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $content = $renderChildrenClosure();
        $content = trim($content);
        $lines = explode("\n", $content);
        $lines = array_map('trim', $lines);
        return implode(LF, $lines);
    }
}
