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
 * ### Format: Append string content
 *
 * Appends a string after another string. Although this task is very
 * easily done in standard Fluid - i.e. {subject}{add} - this
 * ViewHelper makes advanced chained inline processing possible:
 *
 *     <!-- useful when needing to chain string processing. Remove all "foo" and "bar"
 *          then add a text containing both "foo" and "bar", then format as HTML -->
 *     {text -> v:format.eliminate(strings: 'foo,bar')
 *           -> v:format.append(add: ' - my foo and bar are the only ones in this text.')
 *           -> f:format.html()}
 *     <!-- NOTE: you do not have to break the lines; done here only for presentation purposes -->
 *
 * Makes no sense used as tag based ViewHelper:
 *
 *     <!-- DO NOT USE - depicts COUNTERPRODUCTIVE usage! -->
 *     <v:format.append add="{f:translate(key: 're')}">{subject}</v:format.append>
 *     <!-- ... which is the exact same as ... -->
 *     <f:translate key="re" />{subject} <!-- OR --> {f:translate(key: 're')}{subject}
 *
 * In other words: use this only when you do not have the option of
 * simply using {subject}{add}, i.e. in complex inline statements used
 * as attribute values on other ViewHelpers (where tag usage is undesirable).
 */
class AppendViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('subject', 'string', 'String to append other string to');
        $this->registerArgument('add', 'string', 'String to append');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        return $renderChildrenClosure() . $arguments['add'];
    }
}
