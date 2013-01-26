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
 * Returns an array of files found in the provided path
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
class Tx_Vhs_ViewHelpers_Media_ImageViewHelper extends Tx_Vhs_ViewHelpers_Media_AbstractMediaTagViewHelper {

	/**
	 * name of the tag to be created by this view helper
	 *
	 * @var string
	 * @api
	 */
	protected $tagName = 'img';

	/**
	 *
	 * @var string
	 */
	protected $imageSource;

	/**
	 * Result of tslib_cObj::getImgResource()
	 * @var array
	 */
	protected $imageInfo;

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('width', 'int', 'Optional width.', FALSE);
		$this->registerArgument('height', 'int', 'Optional height.', FALSE);

		$this->registerUniversalTagAttributes();

		$this->registerTagAttribute('usemap', 'string', 'A hash-name reference to a map element with which to associate the image.', FALSE);
		$this->registerTagAttribute('ismap', 'string', 'Specifies that its img element provides access to a server-side image map.', FALSE, '');
		$this->registerTagAttribute('alt', 'string', 'Equivalent content for those who cannot process images or who have image loading disabled.', TRUE);
	}

	public function render() {

		$this->preprocess();

		$src = $this->imageSource;

		if (TYPO3_MODE === 'BE' || TRUE === $this->arguments['absUri']) {
			$src = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $src;
		}

		$this->tag->addAttribute('src', $src);
		$this->tag->addAttribute('width', $this->imageInfo[0]);
		$this->tag->addAttribute('height', $this->imageInfo[1]);

		if ($this->arguments['title'] === '') {
			$this->tag->addAttribute('title', $this->arguments['alt']);
		}

		return $this->tag->render();
	}

	/**
	 * @return void
	 */
	public function preprocess() {
		$src = $this->arguments['src'];
		$width = $this->arguments['width'];
		$height = $this->arguments['height'];
		$minW = $this->arguments['minW'];
		$minH = $this->arguments['minH'];
		$maxW = $this->arguments['maxW'];
		$maxH = $this->arguments['maxH'];

		if (TYPO3_MODE === 'BE') {
			$this->simulateFrontendEnvironment();
		}

		$setup = array(
			'width'  => $width,
			'height' => $height,
			'minW'   => $minWidth,
			'minH'   => $minHeight,
			'maxW'   => $maxWidth,
			'maxH'   => $maxHeight
		);

		if (TYPO3_MODE === 'BE' && substr($src, 0, 3) === '../') {
			$src = substr($src, 3);
		}

		$this->imageInfo = $this->contentObject->getImgResource($src, $setup);

		$GLOBALS['TSFE']->lastImageInfo = $this->imageInfo;

		if (!is_array($this->imageInfo)) {
			throw new Tx_Fluid_Core_ViewHelper_Exception('Could not get image resource for "' . htmlspecialchars($src) . '".' , 1253191060);
		}

		$this->imageInfo[3] = t3lib_div::png_to_gif_by_imagemagick($this->imageInfo[3]);

		$GLOBALS['TSFE']->imagesOnPage[] = $this->imageInfo[3];

		$this->imageSource = $GLOBALS['TSFE']->absRefPrefix . t3lib_div::rawUrlEncodeFP($this->imageInfo[3]);

		if (TYPO3_MODE === 'BE') {
			$this->resetFrontendEnvironment();
		}
	}

	/**
	 * Prepares $GLOBALS['TSFE'] for Backend mode
	 * This somewhat hacky work around is currently needed because the getImgResource() function of tslib_cObj relies on those variables to be set
	 *
	 * @return void
	 */
	protected function simulateFrontendEnvironment() {
		$this->tsfeBackup = isset($GLOBALS['TSFE']) ? $GLOBALS['TSFE'] : NULL;
			// set the working directory to the site root
		$this->workingDirectoryBackup = getcwd();
		chdir(PATH_site);

		$typoScriptSetup = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$GLOBALS['TSFE'] = new stdClass();
		$template = t3lib_div::makeInstance('t3lib_TStemplate');
		$template->tt_track = 0;
		$template->init();
		$template->getFileName_backPath = PATH_site;
		$GLOBALS['TSFE']->tmpl = $template;
		$GLOBALS['TSFE']->tmpl->setup = $typoScriptSetup;
		$GLOBALS['TSFE']->config = $typoScriptSetup;
	}

	/**
	 * Resets $GLOBALS['TSFE'] if it was previously changed by simulateFrontendEnvironment()
	 *
	 * @return void
	 * @see simulateFrontendEnvironment()
	 */
	protected function resetFrontendEnvironment() {
		$GLOBALS['TSFE'] = $this->tsfeBackup;
		chdir($this->workingDirectoryBackup);
	}

}
