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

use TYPO3\CMS\Core\Cache\Frontend\StringFrontend;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

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
 * - be executable according to `open_basedir` and others
 * - exist within (one or more of) TYPO3's configured executable paths
 *
 * In other words, *NIX standard behavior must be used.
 *
 * See: http://daringfireball.net/projects/markdown/
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Format
 */
class MarkdownViewHelper extends AbstractViewHelper {

	/**
	 * @var boolean
	 */
	protected $escapingInterceptorEnabled = FALSE;

	/**
	 * @var string
	 */
	protected $markdownExecutablePath;

	/**
	 * @var StringFrontend
	 */
	protected $cache;

	/**
	 * @return void
	 */
	public function initialize() {
		$this->cache = $GLOBALS['typo3CacheManager']->getCache('vhs_markdown');
	}

	/**
	 * @param string $text
	 * @param boolean $trim
	 * @param boolean $htmlentities
	 * @throws Exception
	 * @return string
	 */
	public function render($text = NULL, $trim = TRUE, $htmlentities = FALSE) {
		if (NULL === $text) {
			$text = $this->renderChildren();
		}
		if (NULL === $text) {
			return NULL;
		}

		$cacheIdentifier = sha1($text);
		if (TRUE === $this->cache->has($cacheIdentifier)) {
			return $this->cache->get($cacheIdentifier);
		}

		$this->markdownExecutablePath = \TYPO3\CMS\Core\Utility\CommandUtility::getCommand('markdown');
		if (FALSE === is_executable($this->markdownExecutablePath)) {
			throw new Exception('Use of Markdown requires the "markdown" shell utility to be installed ' .
				'and accessible; this binary could not be found in any of your configured paths available to this script', 1350511561);
		}
		if (TRUE === (boolean) $trim) {
			$text = trim($text);
		}
		if (TRUE === (boolean) $htmlentities) {
			$text = htmlentities($text);
		}
		$transformed = $this->transform($text);
		$this->cache->set($cacheIdentifier, $transformed);
		return $transformed;
	}

	/**
	 * @param string $text
	 * @throws Exception
	 * @return string
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

		if ('' !== trim($errors)) {
			throw new Exception('There was an error while executing ' . $this->markdownExecutablePath . '. The return code was ' .
				$exitCode . ' and the message reads: ' . $errors, 1350514144);
		}

		return $transformed;
	}

}
