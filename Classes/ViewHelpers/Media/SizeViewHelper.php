<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
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
class Tx_Vhs_ViewHelpers_Media_SizeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerArgument('path', 'string', 'Path to the file to determine size for.', FALSE, NULL);
	}

	/**
	 * @throws Tx_Fluid_Core_ViewHelper_Exception
	 * @return integer
	 */
	public function render() {

		$path = $this->arguments['path'];

		if ($path === NULL) {
			$path = $this->renderChildren();
			if ($path === NULL) {
				return 0;
			}
		}

		$file = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($path);

		if (!file_exists($file) || is_dir($file)) {
			throw new Tx_Fluid_Core_ViewHelper_Exception('Cannot determine size of "' . $file . '". File does not exist or is a directory.', 1356953963);
		}

		$size = filesize($file);

		if ($size === FALSE) {
			throw new Tx_Fluid_Core_ViewHelper_Exception('Cannot determine size of "' . $file . '".', 1356954032);
		}

		return $size;
	}

}
