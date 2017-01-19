<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * ### Wordwrap: Wrap a string at provided character count
 *
 * Wraps a string to $limit characters and at $break character
 * while maintaining complete words. Concatenates the resulting
 * strings with $glue. Code is heavily inspired
 * by Codeigniter's word_wrap helper.
 */
class WordWrapViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('subject', 'string', 'Text to wrap');
        $this->registerArgument('limit', 'integer', 'Maximum length of resulting parts after wrapping', false, 80);
        $this->registerArgument('break', 'string', 'Character to wrap text at', false, PHP_EOL);
        $this->registerArgument('glue', 'string', 'Character to concatenate parts with after wrapping', false, PHP_EOL);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $subject = $renderChildrenClosure();
        $limit = (integer) $arguments['limit'];
        $break = $arguments['break'];
        $glue = $arguments['glue'];
        $subject = preg_replace('/ +/', ' ', $subject);
        $subject = str_replace(["\r\n", "\r"], PHP_EOL, $subject);
        $subject = wordwrap($subject, $limit, $break, false);
        $output = '';
        foreach (explode($break, $subject) as $line) {
            if (mb_strlen($line) <= $limit) {
                $output .= $line . $glue;
                continue;
            }
            $temp = '';
            while (mb_strlen($line) > $limit) {
                $temp .= mb_substr($line, 0, $limit - 1);
                $line = mb_substr($line, $limit - 1);
            }
            if (false === empty($temp)) {
                $output .= $temp . $glue . $line . $glue;
            } else {
                $output .= $line . $glue;
            }
        }
        return $output;
    }
}
