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
 * ### Wordwrap: Wrap a string at provided character count
 *
 * Wraps a string to $limit characters and at $break character
 * while maintaining complete words. Concatenates the resulting
 * strings with $glue. Code is heavily inspired
 * by Codeigniter's word_wrap helper.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>
 * @package Vhs
 * @subpackage ViewHelpers\Format
 */
class WordWrapViewHelper extends AbstractViewHelper {

	public function initializeArguments() {
		$this->registerArgument('subject', 'string', 'Text to wrap', FALSE);
		$this->registerArgument('limit', 'integer', 'Maximum length of resulting parts after wrapping', FALSE, 80);
		$this->registerArgument('break', 'string', 'Character to wrap text at', FALSE, PHP_EOL);
		$this->registerArgument('glue', 'string', 'Character to concatenate parts with after wrapping', FALSE, PHP_EOL);
	}

	/**
	 * @return string
	 */
	public function render() {
		$subject = $this->arguments['subject'];
		if (TRUE === empty($subject)) {
			$subject = $this->renderChildren();
		}
		$limit = (integer) $this->arguments['limit'];
		$break = $this->arguments['break'];
		$glue = $this->arguments['glue'];
		$subject = preg_replace('/ +/', ' ', $subject);
		$subject = str_replace(array("\r\n", "\r"), PHP_EOL, $subject);
		$subject = wordwrap($subject, $limit, $break, FALSE);
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
			if (FALSE === empty($temp)) {
				$output .= $temp . $glue . $line . $glue;
			} else {
				$output .= $line . $glue;
			}
		}
		return $output;
	}

}
