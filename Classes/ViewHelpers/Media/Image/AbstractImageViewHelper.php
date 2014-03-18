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
 * Base class for image related view helpers adapted from FLUID
 * original image viewhelper.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
abstract class Tx_Vhs_ViewHelpers_Media_Image_AbstractImageViewHelper extends Tx_Vhs_ViewHelpers_Media_AbstractMediaViewHelper {

	/**
	 * @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController contains a backup of the current $GLOBALS['TSFE'] if used in BE mode
	 */
	protected $tsfeBackup;

	/**
	 * @var string
	 */
	protected $workingDirectoryBackup;

	/**
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	protected $contentObject;

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * Result of \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::getImgResource()
	 * @var array
	 */
	protected $imageInfo;

	/**
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager) {
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
		parent::initializeArguments();
		$this->registerArgument('width', 'string', 'Width of the image. This can be a numeric value representing the fixed width of the image in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width for possible options.', FALSE);
		$this->registerArgument('height', 'string', 'Height of the image. This can be a numeric value representing the fixed height of the image in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width for possible options.', FALSE);
		$this->registerArgument('treatIdAsReference', 'boolean', 'When TRUE treat given src argument as sys_file_reference record. Applies only to TYPO3 6.x and above.', FALSE, FALSE);
	}

	/**
	 * @throws Tx_Fluid_Core_ViewHelper_Exception
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
		$treatIdAsReference = (boolean) $this->arguments['treatIdAsReference'];

		if (TYPO3_MODE === 'BE') {
			$this->simulateFrontendEnvironment();
		}
		$setup = array(
			'width' => $width,
			'height' => $height,
			'minW' => $minW,
			'minH' => $minH,
			'maxW' => $maxW,
			'maxH' => $maxH,
			'treatIdAsReference' => $treatIdAsReference,
		);
		if (TYPO3_MODE === 'BE' && substr($src, 0, 3) === '../') {
			$src = substr($src, 3);
		}
		$this->imageInfo = $this->contentObject->getImgResource($src, $setup);
		$GLOBALS['TSFE']->lastImageInfo = $this->imageInfo;
		if (!is_array($this->imageInfo)) {
			throw new Tx_Fluid_Core_ViewHelper_Exception('Could not get image resource for "' . htmlspecialchars($src) . '".', 1253191060);
		}
		$this->imageInfo[3] = \TYPO3\CMS\Core\Utility\GeneralUtility::png_to_gif_by_imagemagick($this->imageInfo[3]);
		$GLOBALS['TSFE']->imagesOnPage[] = $this->imageInfo[3];
		$this->mediaSource = $GLOBALS['TSFE']->absRefPrefix . \TYPO3\CMS\Core\Utility\GeneralUtility::rawUrlEncodeFP($this->imageInfo[3]);
		if (TYPO3_MODE === 'BE') {
			$this->resetFrontendEnvironment();
		}
	}

	/**
	 * Prepares $GLOBALS['TSFE'] for Backend mode
	 * This somewhat hacky work around is currently needed because the getImgResource() function of \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer relies on those variables to be set
	 *
	 * @return void
	 */
	protected function simulateFrontendEnvironment() {
		$this->tsfeBackup = isset($GLOBALS['TSFE']) ? $GLOBALS['TSFE'] : NULL;
		$this->workingDirectoryBackup = getcwd();
		chdir(constant('PATH_site'));
		$typoScriptSetup = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$GLOBALS['TSFE'] = new stdClass();
		$template = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\TypoScript\TemplateService');
		$template->tt_track = 0;
		$template->init();
		$template->getFileName_backPath = constant('PATH_site');
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
