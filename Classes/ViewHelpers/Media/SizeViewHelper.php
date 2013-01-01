<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
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
 * Returns the size of the provided file in bytes
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
class Tx_Vhs_ViewHelpers_Media_SizeViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerArgument('path', 'string', 'Path to the file to determine size for.', TRUE);
	}

	/**
	 * @return int
	 */
	public function render() {

		$path = $this->arguments['path'];

		if ($path === NULL) {
			$path = $this->renderChildren();
			if ($path === NULL) {
				return 0;
			}
		}

		if (!file_exists($path) || is_dir($path)) {
			throw new Tx_Fluid_Core_ViewHelper_Exception('Cannot determine size of "' . $path . '". File does not exist or is a directory.', 1356953963);            
		}

		$size = filesize($path);

		if ($size === FALSE) {
			throw new Tx_Fluid_Core_ViewHelper_Exception('Cannot determine size of "' . $path . '".', 1356954032);
		}

		return $size;
	}

}
