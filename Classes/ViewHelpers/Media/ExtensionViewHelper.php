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
 * Returns the extension of the provided file
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
class Tx_Vhs_ViewHelpers_Media_ExtensionViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerArgument('file', 'string', 'Path to the file to determine extension for.', TRUE);
	}

	/**
	 * @return string
	 */
	public function render() {

		$filePath = $this->arguments['file'];

		if ($filePath === NULL) {
			$filePath = $this->renderChildren();

			if ($filePath === NULL) {
				return '';
			}
		}

		$file = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($filePath);

		$parts = explode('.', basename($file));

		// file has no extension
		if (count($parts) == 1) {
			return '';
		}

		$extension = strtolower(array_pop($parts));

		return $extension;
	}

}
