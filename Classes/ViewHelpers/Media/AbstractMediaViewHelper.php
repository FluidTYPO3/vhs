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
 * Base class for media related view helpers (only images currently)
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
class Tx_Vhs_ViewHelpers_Media_AbstractMediaViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractTagBasedViewHelper {

	/**
	 * @var tslib_cObj
	 */
	protected $contentObject;

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 *
	 * @var string
	 */
	protected $mediaSource;

	/**
	 * Result of tslib_cObj::getImgResource()
	 * @var array
	 */
	protected $imageInfo;

	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
		$this->contentObject = $this->configurationManager->getContentObject();
	}

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerArgument('src', 'string', 'Path to the media resource.', TRUE);
		$this->registerArgument('relative', 'boolean', 'If FALSE media URIs are rendered absolute. URIs in backend mode are always absolute.', FALSE, TRUE);
	}

	/**
	 * Turns a relative source URL into an absolute URL
	 * if required
	 *
	 * @param string $src
	 * @return string
	 */
	public function preprocessSourceUrl($src) {
		if (TYPO3_MODE === 'BE' || FALSE === $this->arguments['relative']) {
			$src = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $src;
		}

		return $src;
	}


	/**
	 *
	 * @return void
	 */
	public function preprocessImage() {
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

		$this->mediaSource = $GLOBALS['TSFE']->absRefPrefix . t3lib_div::rawUrlEncodeFP($this->imageInfo[3]);

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
