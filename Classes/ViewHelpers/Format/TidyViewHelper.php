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
 * Tidy-processes a string (HTML source), applying proper
 * indentation.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Format
 */
class TidyViewHelper extends AbstractViewHelper {

	/**
	 * @var boolean
	 */
	protected $hasTidy = FALSE;

	/**
	 * @return void
	 */
	public function initialize() {
		$this->hasTidy = class_exists('tidy');
	}

	/**
	 * Trims content, then trims each line of content
	 *
	 * @param string $content
	 * @param string $encoding
	 * @throws \RuntimeException
	 * @return string
	 */
	public function render($content = NULL, $encoding = 'utf8') {
		if (NULL === $content) {
			$content = $this->renderChildren();
		}
		if (TRUE === $this->hasTidy) {
			$tidy = tidy_parse_string($content, array(), $encoding);
			$tidy->cleanRepair();
			return (string) $tidy;
		}
		throw new \RuntimeException('TidyViewHelper requires the PHP extension "tidy" which is not installed or not loaded.', 1352059753);
	}

}
