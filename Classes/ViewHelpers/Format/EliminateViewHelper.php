<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Character/string/whitespace elimination ViewHelper
 *
 * There is no example - each argument describes how it should be
 * used and arguments can be used individually or in any combination.
 */
class EliminateViewHelper extends AbstractViewHelper
{

    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments()
    {
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
     * @param string $content
     * @return string
     */
    public function render($content = null)
    {
        if (null === $content) {
            $content = $this->renderChildren();
        }
        if (true === isset($this->arguments['characters'])) {
            $content = $this->eliminateCharacters($content, $this->arguments['characters']);
        }
        if (true === isset($this->arguments['strings'])) {
            $content = $this->eliminateStrings($content, $this->arguments['strings']);
        }
        if (true === $this->arguments['whitespace']) {
            $content = $this->eliminateWhitespace($content);
        }
        if (true === $this->arguments['whitespaceBetweenHtmlTags']) {
            $content = $this->eliminateWhitespaceBetweenHtmlTags($content);
        }
        if (true === $this->arguments['tabs']) {
            $content = $this->eliminateTabs($content);
        }
        if (true === $this->arguments['unixBreaks']) {
            $content = $this->eliminateUnixBreaks($content);
        }
        if (true === $this->arguments['windowsBreaks']) {
            $content = $this->eliminateWindowsCarriageReturns($content);
        }
        if (true === $this->arguments['digits']) {
            $content = $this->eliminateDigits($content);
        }
        if (true === $this->arguments['letters']) {
            $content = $this->eliminateLetters($content);
        }
        if (true === $this->arguments['nonAscii']) {
            $content = $this->eliminateNonAscii($content);
        }
        return $content;
    }

    /**
     * @param string $content
     * @param mixed $characters
     * @return string
     */
    protected function eliminateCharacters($content, $characters)
    {
        $caseSensitive = (boolean) $this->arguments['caseSensitive'];
        if (true === is_array($characters)) {
            $subjects = $characters;
        } else {
            $subjects = str_split($characters);
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
     * @return string
     */
    protected function eliminateStrings($content, $strings)
    {
        $caseSensitive = (boolean) $this->arguments['caseSensitive'];
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
    protected function eliminateWhitespace($content)
    {
        $content = preg_replace('/\s+/', '', $content);
        return $content;
    }

    /**
     * @param string $content
     * @return string
     */
    protected function eliminateWhitespaceBetweenHtmlTags($content)
    {
        $content = trim(preg_replace('/>\s+</', '><', $content));
        return $content;
    }

    /**
     * @param string $content
     * @return string
     */
    protected function eliminateTabs($content)
    {
        $content = str_replace("\t", '', $content);
        return $content;
    }

    /**
     * @param string $content
     * @return string
     */
    protected function eliminateUnixBreaks($content)
    {
        $content = str_replace("\n", '', $content);
        return $content;
    }

    /**
     * @param string $content
     * @return string
     */
    protected function eliminateWindowsCarriageReturns($content)
    {
        $content = str_replace("\r", '', $content);
        return $content;
    }

    /**
     * @param string $content
     * @return string
     */
    protected function eliminateDigits($content)
    {
        $content = preg_replace('#[0-9]#', '', $content);
        return $content;
    }

    /**
     * @param string $content
     * @return string
     */
    protected function eliminateLetters($content)
    {
        $caseSensitive = (boolean) $this->arguments['caseSensitive'];
        if (true === $caseSensitive) {
            $content = preg_replace('#[a-z]#', '', $content);
        } else {
            $content = preg_replace('/[a-z]/i', '', $content);
        }
        return $content;
    }

    /**
     * @param string $content
     * @return string
     */
    protected function eliminateNonAscii($content)
    {
        $caseSensitive = (boolean) $this->arguments['caseSensitive'];
        $caseSensitiveIndicator = true === $caseSensitive ? 'i' : '';
        $content = preg_replace('/[^(\x20-\x7F)]*/' . $caseSensitiveIndicator, '', $content);
        return $content;
    }
}
