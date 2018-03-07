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
 * Replaces $substring in $content with $replacement.
 */
class ReplaceViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('content', 'string', 'Content in which to perform replacement');
        $this->registerArgument('substring', 'string', 'Substring to replace. Using constants is possible with "constant:NAMEOFCONSTANT" (for example: constant:LF" for linefeed)', true);
        $this->registerArgument('replacement', 'string', 'Replacement to insert. Using constants is possible with "constant:NAMEOFCONSTANT" (for example: constant:LF" for linefeed)', false, '');
        $this->registerArgument('count', 'integer', 'Maximum number of times to perform replacement');
        $this->registerArgument('caseSensitive', 'boolean', 'If true, perform case-sensitive replacement', false, true);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $content = $renderChildrenClosure();
        $substring = static::resolveValue($arguments['substring']);
        $replacement = static::resolveValue($arguments['replacement']);
        $count = (integer) $arguments['count'];
        $caseSensitive = (boolean) $arguments['caseSensitive'];
        $function = (true === $caseSensitive ? 'str_replace' : 'str_ireplace');
        return $function($substring, $replacement, $content, $count);
    }

    /**
     * Resolve value (special handling for constants)
     *
     * @param string $source
     * @return string
     */
    protected static function resolveValue($source)
    {
        $return = $source;
        if (false !== mb_strpos($source, ':') && 1 < mb_strlen($source)) {
            // glue contains a special type identifier, resolve the actual glue
            list ($type, $value) = explode(':', $source);
            switch ($type) {
                case 'constant':
                    $return = constant($value);
                    break;
                default:
                    $return = $value;
            }
        }
        return $return;
    }
}
