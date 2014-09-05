<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Character/string/whitespace elimination ViewHelper
 *
 * There is no example - each argument describes how it should be
 * used and arguments can be used individually or in any combination.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Format
 */
class EliminateViewHelper extends AbstractViewHelper {

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('caseSensitive', 'boolean', 'Wether or not to perform case sensitive replacement', FALSE, TRUE);
		$this->registerArgument('characters', 'mixed', "Characters to remove. Array or string, i.e. {0: 'a', 1: 'b', 2: 'c'} or 'abc' to remove all occurrences of a, b and c");
		$this->registerArgument('strings', 'mixed', "Strings to remove. Array or CSV, i.e. {0: 'foo', 1: 'bar'} or 'foo,bar' to remove all occorrences of foo and bar. If your strings overlap then place the longest match first");
		$this->registerArgument('whitespace', 'boolean', 'Eliminate ALL whitespace characters', FALSE, FALSE);
		$this->registerArgument('tabs', 'boolean', 'Eliminate only tab whitespaces', FALSE, FALSE);
		$this->registerArgument('unixBreaks', 'boolean', 'Eliminate only UNIX line breaks', FALSE, FALSE);
		$this->registerArgument('windowsBreaks', 'boolean', 'Eliminates only Windows carriage returns', FALSE, FALSE);
		$this->registerArgument('digits', 'boolean', 'Eliminates all number characters (but not the dividers between floats converted to strings)', FALSE, FALSE);
		$this->registerArgument('letters', 'boolean', 'Eliminates all letters (non-numbers, non-whitespace, non-syntactical)', FALSE, FALSE);
		$this->registerArgument('nonAscii', 'boolean', 'Eliminates any ASCII char', FALSE, FALSE);
	}

	/**
	 * @param string $content
	 * @return string
	 */
	public function render($content = NULL) {
		if (NULL === $content) {
			$content = $this->renderChildren();
		}
		if (TRUE === isset($this->arguments['characters'])) {
			$content = $this->eliminateCharacters($content, $this->arguments['characters']);
		}
		if (TRUE === isset($this->arguments['strings'])) {
			$content = $this->eliminateStrings($content, $this->arguments['strings']);
		}
		if (TRUE === $this->arguments['whitespace']) {
			$content = $this->eliminateWhitespace($content);
		}
		if (TRUE === $this->arguments['tabs']) {
			$content = $this->eliminateTabs($content);
		}
		if (TRUE === $this->arguments['unixBreaks']) {
			$content = $this->eliminateUnixBreaks($content);
		}
		if (TRUE === $this->arguments['windowsBreaks']) {
			$content = $this->eliminateWindowsCarriageReturns($content);
		}
		if (TRUE === $this->arguments['digits']) {
			$content = $this->eliminateDigits($content);
		}
		if (TRUE === $this->arguments['letters']) {
			$content = $this->eliminateLetters($content);
		}
		if (TRUE === $this->arguments['nonAscii']) {
			$content = $this->eliminateNonAscii($content);
		}
		return $content;
	}

	/**
	 * @param string $content
	 * @param mixed $characters
	 * @return string
	 */
	protected function eliminateCharacters($content, $characters) {
		$caseSensitive = (boolean) $this->arguments['caseSensitive'];
		if (TRUE === is_array($characters)) {
			$subjects = $characters;
		} else {
			$subjects = str_split($characters);
		}
		foreach ($subjects as $subject) {
			if (TRUE === $caseSensitive) {
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
	protected function eliminateStrings($content, $strings) {
		$caseSensitive = (boolean) $this->arguments['caseSensitive'];
		if (TRUE === is_array($strings)) {
			$subjects = $strings;
		} else {
			$subjects = explode(',', $strings);
		}
		foreach ($subjects as $subject) {
			if (TRUE === $caseSensitive) {
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
	protected function eliminateWhitespace($content) {
		$content = preg_replace('/\s+/', '', $content);
		return $content;
	}

	/**
	 * @param string $content
	 * @return string
	 */
	protected function eliminateTabs($content) {
		$content = str_replace("\t", '', $content);
		return $content;
	}

	/**
	 * @param string $content
	 * @return string
	 */
	protected function eliminateUnixBreaks($content) {
		$content = str_replace("\n", '', $content);
		return $content;
	}

	/**
	 * @param string $content
	 * @return string
	 */
	protected function eliminateWindowsCarriageReturns($content) {
		$content = str_replace("\r", '', $content);
		return $content;
	}

	/**
	 * @param string $content
	 * @return string
	 */
	protected function eliminateDigits($content) {
		$content = preg_replace('#[0-9]#', '', $content);
		return $content;
	}

	/**
	 * @param string $content
	 * @return string
	 */
	protected function eliminateLetters($content) {
		$caseSensitive = (boolean) $this->arguments['caseSensitive'];
		if (TRUE === $caseSensitive) {
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
	protected function eliminateNonAscii($content) {
		$caseSensitive = (boolean) $this->arguments['caseSensitive'];
		$caseSensitiveIndicator = TRUE === $caseSensitive ? 'i' : '';
		$content = preg_replace('/[^(\x20-\x7F)]*/' . $caseSensitiveIndicator, '', $content);
		return $content;
	}

}
