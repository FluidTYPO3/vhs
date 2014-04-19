<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media\Image;
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
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * Base class: Media\Image view helpers
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Media\Image
 */
abstract class AbstractImageInfoViewHelper extends AbstractViewHelper {

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerArgument('path', 'string', 'Path to the image file to determine info for.', TRUE);
	}

	/**
	 * @throws Exception
	 * @return array
	 */
	public function getInfo() {

		$path = $this->arguments['path'];

		if (NULL === $path) {
			$path = $this->renderChildren();
			if (NULL === $path) {
				return array();
			}
		}

		$file = GeneralUtility::getFileAbsFileName($path);

		if (FALSE === file_exists($file) || TRUE === is_dir($file)) {
			throw new Exception('Cannot determine info for "' . $file . '". File does not exist or is a directory.', 1357066532);
		}

		$info = getimagesize($file);

		return array(
			'width'  => $info[0],
			'height' => $info[1],
			'type'   => $info['mime'],
		);
	}

}
