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
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * Returns the size of the provided file in bytes
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
class SizeViewHelper extends AbstractViewHelper {

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
	 * @throws Exception
	 * @return integer
	 */
	public function render() {

		$path = $this->arguments['path'];

		if (NULL === $path) {
			$path = $this->renderChildren();
			if (NULL === $path) {
				return 0;
			}
		}

		$file = GeneralUtility::getFileAbsFileName($path);

		if (FALSE === file_exists($file) || TRUE === is_dir($file)) {
			throw new Exception('Cannot determine size of "' . $file . '". File does not exist or is a directory.', 1356953963);
		}

		$size = filesize($file);

		if (FALSE === $size) {
			throw new Exception('Cannot determine size of "' . $file . '".', 1356954032);
		}

		return $size;
	}

}
