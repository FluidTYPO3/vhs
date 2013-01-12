<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Claus Due <claus@wildside.dk>, Wildside A/S
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

/**
 * Markdown Transformation ViewHelper
 *
 * Requires an installed "markdown" utility, the specific
 * implementation is less important since Markdown has no
 * configuration options. However, the utility or shell
 * scipt must:
 *
 * - accept input from STDIN
 * - output to STDOUT
 * - place errors in STDERR
 *
 * In other words, *NIX standard behavior must be used.
 *
 * See: http://daringfireball.net/projects/markdown/
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers\Format
 */
class Tx_Vhs_ViewHelpers_Format_MarkdownViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var boolean
	 */
	protected $escapingInterceptorEnabled = FALSE;

	/**
	 * @var string
	 */
	protected $markdownExecutablePath;

	/**
	 * @param string $text
	 * @param boolean $trim
	 * @param boolean $htmlentities
	 * @return string
	 */
	public function render($text = NULL, $trim = TRUE, $htmlentities = FALSE) {
		$this->markdownExecutablePath = trim(shell_exec('which markdown'));
		if (is_executable($this->markdownExecutablePath) === FALSE) {
			throw new Exception('Use of Markdown requires the "markdown" binary to be installed an accessible; this binary could' .
				' not be found in any of your configured paths available to this script', 1350511561);
		}
		if ($text === NULL) {
			$text = $this->renderChildren();
		}
		if ($trim) {
			$text = trim($text);
		}
		if ($htmlentities) {
			$text = htmlentities($text);
		}
		$transformed = $this->transform($text);
		return $transformed;
	}

	/**
	 * @param string $text
	 */
	public function transform($text) {
		$descriptorspec = array(
			0 => array('pipe', 'r'),
			1 => array('pipe', 'w'),
			2 => array('pipe', 'a')
		);

		$process = proc_open($this->markdownExecutablePath, $descriptorspec, $pipes, NULL, $GLOBALS['_ENV']);

		stream_set_blocking($pipes[0], 1);
		stream_set_blocking($pipes[1], 1);
		stream_set_blocking($pipes[2], 1);

		fwrite($pipes[0], $text);
		fclose($pipes[0]);

		$transformed = stream_get_contents($pipes[1]);
		fclose($pipes[1]);

		$errors = stream_get_contents($pipes[2]);
		fclose($pipes[2]);

		$exitCode = proc_close($process);

		if (trim($errors) !== '') {
			throw new Exception('There was an error while executing ' . $this->markdownExecutablePath . '. The return code was ' .
				$exitCode . ' and the message reads: ' . $errors, 1350514144);
		}

		return $transformed;
	}

}