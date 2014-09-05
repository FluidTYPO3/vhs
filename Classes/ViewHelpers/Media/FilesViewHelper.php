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
 * Returns an array of files found in the provided path
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
class FilesViewHelper extends AbstractViewHelper {

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerArgument('path', 'string', 'Path to the folder containing the files to be listed.', TRUE);
		$this->registerArgument('extensionList', 'string', 'A comma seperated list of file extensions to pick up.', FALSE, '');
		$this->registerArgument('prependPath', 'boolean', 'If set to TRUE the path will be prepended to file names.', FALSE, FALSE);
		$this->registerArgument('order', 'string', 'If set to "mtime" sorts files by modification time or alphabetically otherwise.', FALSE, '');
		$this->registerArgument('excludePattern', 'string', 'A comma seperated list of filenames to exclude, no wildcards.', FALSE, '');
	}

	/**
	 * @return array
	 */
	public function render() {
		$path = $this->arguments['path'];

		if (NULL === $path) {
			$path = $this->renderChildren();
			if (NULL === $path) {
				return array();
			}
		}

		$extensionList  = $this->arguments['extensionList'];
		$prependPath    = $this->arguments['prependPath'];
		$order          = $this->arguments['order'];
		$excludePattern = $this->arguments['excludePattern'];

		$files = GeneralUtility::getFilesInDir($path, $extensionList, $prependPath, $order, $excludePattern);

		return $files;
	}

}
