<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;

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

    public function initializeArguments(): void
    {
        $this->registerArgument('subject', 'string', 'Text to wrap');
        $this->registerArgument('limit', 'integer', 'Maximum length of resulting parts after wrapping', false, 80);
        $this->registerArgument('break', 'string', 'Character to wrap text at', false, PHP_EOL);
        $this->registerArgument('glue', 'string', 'Character to concatenate parts with after wrapping', false, PHP_EOL);
    }

    /**
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        /** @var string $subject */
        $subject = $renderChildrenClosure();
        /** @var int $limit */
        $limit = $arguments['limit'];
        /** @var non-empty-string $break */
        $break = $arguments['break'];
        /** @var string $glue */
        $glue = $arguments['glue'];
        /** @var string $subject */
        $subject = preg_replace('/ +/', ' ', $subject);
        $subject = str_replace(["\r\n", "\r"], PHP_EOL, $subject);
        if (is_array($subject)) {
            return $subject;
        }
        $subject = wordwrap($subject, $limit, $break);
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
            if (!empty($temp)) {
                $output .= $temp . $glue . $line . $glue;
            } else {
                $output .= $line . $glue;
            }
        }
        return $output;
    }
}
