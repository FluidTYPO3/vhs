<?php
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
 ***************************************************************/

/**
 * Tidy-processes a string (HTML source), applying proper
 * indentation.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Format
 */
class Tx_Vhs_ViewHelpers_Format_TidyViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

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
	 * @throws Exception
	 * @return string
	 */
	public function render($content = NULL) {
		if (NULL === $content) {
			$content = $this->renderChildren();
		}
		if (TRUE === $this->hasTidy) {
			$tidy = tidy_parse_string($content);
			$tidy->cleanRepair();
			return (string) $tidy;
		}
		throw new RuntimeException('TidyViewHelper requires the PHP extension "tidy" which is not installed or not loaded.', 1352059753);
	}

}
