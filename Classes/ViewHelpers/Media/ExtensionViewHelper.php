<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;
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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Returns the extension of the provided file
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
class ExtensionViewHelper extends AbstractViewHelper {

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

		if (NULL === $filePath) {
			$filePath = $this->renderChildren();

			if (NULL === $filePath) {
				return '';
			}
		}

		$file = GeneralUtility::getFileAbsFileName($filePath);

		$parts = explode('.', basename($file));

		// file has no extension
		if (1 === count($parts)) {
			return '';
		}

		$extension = strtolower(array_pop($parts));

		return $extension;
	}

}
