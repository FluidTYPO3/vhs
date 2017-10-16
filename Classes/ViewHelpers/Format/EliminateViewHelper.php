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
 * Character/string/whitespace elimination ViewHelper
 *
 * There is no example - each argument describes how it should be
 * used and arguments can be used individually or in any combination.
 */
class EliminateViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('content', 'string', 'String in which to perform replacement');
        $this->registerArgument(
            'caseSensitive',
            'boolean',
            'Wether or not to perform case sensitive replacement',
            false,
            true
        );
        $this->registerArgument(
            'characters',
            'mixed',
            "Characters to remove. Array or string, i.e. {0: 'a', 1: 'b', 2: 'c'} or 'abc' to remove all " .
            'occurrences of a, b and c'
        );
        $this->registerArgument(
            'strings',
            'mixed',
            "Strings to remove. Array or CSV, i.e. {0: 'foo', 1: 'bar'} or 'foo,bar' to remove all occorrences " .
            'of foo and bar. If your strings overlap then place the longest match first'
        );
        $this->registerArgument('whitespace', 'boolean', 'Eliminate ALL whitespace characters', false, false);
        $this->registerArgument(
            'whitespaceBetweenHtmlTags',
            'boolean',
            'Eliminate ALL whitespace characters between HTML tags',
            false,
            false
        );
        $this->registerArgument('tabs', 'boolean', 'Eliminate only tab whitespaces', false, false);
        $this->registerArgument('unixBreaks', 'boolean', 'Eliminate only UNIX line breaks', false, false);
        $this->registerArgument('windowsBreaks', 'boolean', 'Eliminates only Windows carriage returns', false, false);
        $this->registerArgument(
            'digits',
            'boolean',
            'Eliminates all number characters (but not the dividers between floats converted to strings)',
            false,
            false
        );
        $this->registerArgument(
            'letters',
            'boolean',
            'Eliminates all letters (non-numbers, non-whitespace, non-syntactical)',
            false,
            false
        );
        $this->registerArgument('nonAscii', 'boolean', 'Eliminates any ASCII char', false, false);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $content = $renderChildrenClosure();
        if (true === isset($arguments['characters'])) {
            $content = static::eliminateCharacters($content, $arguments['characters'], (boolean) $arguments['caseSensitive']);
        }
        if (true === isset($arguments['strings'])) {
            $content = static::eliminateStrings($content, $arguments['strings'], (boolean) $arguments['caseSensitive']);
        }
        if (true === $arguments['whitespace']) {
            $content = static::eliminateWhitespace($content);
        }
        if (true === $arguments['whitespaceBetweenHtmlTags']) {
            $content = static::eliminateWhitespaceBetweenHtmlTags($content);
        }
        if (true === $arguments['tabs']) {
            $content = static::eliminateTabs($content);
        }
        if (true === $arguments['unixBreaks']) {
            $content = static::eliminateUnixBreaks($content);
        }
        if (true === $arguments['windowsBreaks']) {
            $content = static::eliminateWindowsCarriageReturns($content);
        }
        if (true === $arguments['digits']) {
            $content = static::eliminateDigits($content);
        }
        if (true === $arguments['letters']) {
            $content = static::eliminateLetters($content, (boolean) $arguments['caseSensitive']);
        }
        if (true === $arguments['nonAscii']) {
            $content = static::eliminateNonAscii($content, (boolean) $arguments['caseSensitive']);
        }
        return $content;
    }

    /**
     * @param string $content
     * @param mixed $characters
     * @param boolean $caseSensitive
     * @return string
     */
    protected static function eliminateCharacters($content, $characters, $caseSensitive)
    {
        if (true === is_array($characters)) {
            $subjects = $characters;
        } else {
            $subjects = preg_split('//u', $characters, null, PREG_SPLIT_NO_EMPTY);
        }
        foreach ($subjects as $subject) {
            if (true === $caseSensitive) {
                $content = str_replace($subject, '', $content);
            } else {
                $content = str_ireplace($subject, '', $content);
            }
        }
        return $content;
    }

    /**
     * @param string $content
     * @param mixed $strings
     * @param boolean $caseSensitive
     * @return string
     */
    protected static function eliminateStrings($content, $strings, $caseSensitive)
    {
        if (true === is_array($strings)) {
            $subjects = $strings;
        } else {
            $subjects = explode(',', $strings);
        }
        foreach ($subjects as $subject) {
            if (true === $caseSensitive) {
                $content = str_replace($subject, '', $content);
            } else {
                $content = str_ireplace($subject, '', $content);
            }
        }
        return $content;
    }

    /**
     * @param string $content
     * @return string
     */
    protected static function eliminateWhitespace($content)
    {
        $content = preg_replace('/\s+/', '', $content);
        return $content;
    }

    /**
     * @param string $content
     * @return string
     */
    protected static function eliminateWhitespaceBetweenHtmlTags($content)
    {
        $content = trim(preg_replace('/>\s+</', '><', $content));
        return $content;
    }

    /**
     * @param string $content
     * @return string
     */
    protected static function eliminateTabs($content)
    {
        $content = str_replace("\t", '', $content);
        return $content;
    }

    /**
     * @param string $content
     * @return string
     */
    protected static function eliminateUnixBreaks($content)
    {
        $content = str_replace("\n", '', $content);
        return $content;
    }

    /**
     * @param string $content
     * @return string
     */
    protected static function eliminateWindowsCarriageReturns($content)
    {
        $content = str_replace("\r", '', $content);
        return $content;
    }

    /**
     * @param string $content
     * @return string
     */
    protected static function eliminateDigits($content)
    {
        $content = preg_replace('#[0-9]#', '', $content);
        return $content;
    }

    /**
     * @param string $content
     * @param boolean $caseSensitive
     * @return string
     */
    protected static function eliminateLetters($content, $caseSensitive)
    {
        if (true === $caseSensitive) {
            $content = preg_replace('#[a-z]#', '', $content);
        } else {
            $content = preg_replace('/[a-z]/i', '', $content);
        }
        return $content;
    }

    /**
     * @param string $content
     * @param boolean $caseSensitive
     * @return string
     */
    protected static function eliminateNonAscii($content, $caseSensitive)
    {
        $caseSensitiveIndicator = true === $caseSensitive ? 'i' : '';
        $content = preg_replace('/[^(\x20-\x7F)]*/' . $caseSensitiveIndicator, '', $content);
        return $content;
    }
}
