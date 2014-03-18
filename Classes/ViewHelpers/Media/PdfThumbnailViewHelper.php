<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
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
 * Converts the provided PDF file into a PNG thumbnail and renders
 * the according image tag using Fluid's standard image ViewHelper
 * thus implementing its arguments. For PDF documents with multiple
 * pages the first page is rendered by default unless specified.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
class Tx_Vhs_ViewHelpers_Media_PdfThumbnailViewHelper extends Tx_Fluid_ViewHelpers_ImageViewhelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('path', 'string', 'Path to PDF source file');
		$this->registerArgument('width', 'integer', 'Width of resulting thumbnail image. See imgResource.width for possible options', FALSE, NULL);
		$this->registerArgument('height', 'integer', 'Height of resulting thumbnail image. See imgResource.width for possible options', FALSE, NULL);
		$this->registerArgument('minWidth', 'integer', 'Minimum width of resulting thumbnail image', FALSE, NULL);
		$this->registerArgument('minHeight', 'integer', 'Minimum height of resulting thumbnail image', FALSE, NULL);
		$this->registerArgument('maxWidth', 'integer', 'Maximum width of resulting thumbnail image', FALSE, NULL);
		$this->registerArgument('maxHeight', 'integer', 'Maximum height of resulting thumbnail image', FALSE, NULL);
		$this->registerArgument('density', 'integer', 'Canvas resolution for rendering the PDF in dpi (higher means better quality)', FALSE, 100);
		$this->registerArgument('background', 'string', 'Fill background of resulting image with this color (for transparent source files)', FALSE, NULL);
		$this->registerArgument('rotate', 'integer', 'Number of degress to rotate resulting image by (caution: very slow if not multiple of 90)', FALSE, 0);
		$this->registerArgument('page', 'integer', 'Optional page number to render as thumbnail for PDF documents with multiple pages', FALSE, 1);
		$this->registerArgument('forceOverwrite', 'boolean', 'Forcibly overwrite existing converted PDF files', FALSE, FALSE);
	}

	/**
	 * @return string
	 */
	public function render() {
		$path = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($this->arguments['path']);
		if (FALSE === file_exists($path)) {
			return NULL;
		}
		$density = $this->arguments['density'];
		$rotate = $this->arguments['rotate'];
		$page = intval($this->arguments['page']);
		$background = $this->arguments['background'];
		$forceOverwrite = (boolean) $this->arguments['forceOverwrite'];
		$width = $this->arguments['width'];
		$height = $this->arguments['height'];
		$minWidth = $this->arguments['minWidth'];
		$minHeight = $this->arguments['minHeight'];
		$maxWidth = $this->arguments['maxWidth'];
		$maxHeight = $this->arguments['maxHeight'];
		$filename = basename($path);
		$pageArgument = $page > 0 ? $page - 1 : 0;
		$colorspace = TRUE === isset($GLOBALS['TYPO3_CONF_VARS']['GFX']['colorspace']) ? $GLOBALS['TYPO3_CONF_VARS']['GFX']['colorspace'] : 'RGB';
		$destination = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('typo3temp/vhs-pdf-' . $filename . '-page' . $page . '.png');
		if (FALSE === file_exists($destination) || TRUE === $forceOverwrite) {
			$arguments = '-colorspace ' . $colorspace;
			if (0 < intval($density)) {
				$arguments .= ' -density ' . $density;
			}
			if (0 !== intval($rotate)) {
				$arguments .= ' -rotate ' . $rotate;
			}
			$arguments .= ' "' . $path . '"[' . $pageArgument . ']';
			if (NULL !== $background) {
				$arguments .= ' -background "' . $background . '" -flatten';
			}
			$arguments .= ' "' . $destination . '"';
			$command = \TYPO3\CMS\Core\Utility\CommandUtility::imageMagickCommand('convert', $arguments);
			\TYPO3\CMS\Core\Utility\CommandUtility::exec($command);
		}
		$image = substr($destination, strlen(PATH_site));
		return parent::render($image, $width, $height, $minWidth, $minHeight, $maxWidth, $maxHeight);
	}

}
