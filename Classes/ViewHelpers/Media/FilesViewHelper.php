<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

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
