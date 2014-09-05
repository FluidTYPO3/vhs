<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
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
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Resource
 */
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use FluidTYPO3\Vhs\Utility\ResourceUtility;

abstract class AbstractImageViewHelper extends AbstractResourceViewHelper {

	/**
	 * @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController contains a backup of the current $GLOBALS['TSFE'] if used in BE mode
	 */
	protected $tsfeBackup;

	/**
	 * @var string
	 */
	protected $workingDirectoryBackup;

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	protected $contentObject;

	/**
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager) {
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
		$this->registerArgument('relative', 'boolean', 'If FALSE resource URIs are rendered absolute. URIs in backend mode are always absolute.', FALSE, TRUE);
		$this->registerArgument('width', 'string', 'Width of the image. Numeric value in pixels or simple calculations. See imgResource.width for possible options.', FALSE, NULL);
		$this->registerArgument('height', 'string', 'Height of the image. Numeric value in pixels or simple calculations. See imgResource.width for possible options.', FALSE, NULL);
		$this->registerArgument('minWidth', 'string', 'Minimum width of the image. Numeric value in pixels or simple calculations. See imgResource.width for possible options.', FALSE, NULL);
		$this->registerArgument('minHeight', 'string', 'Minimum height of the image. Numeric value in pixels or simple calculations. See imgResource.width for possible options.', FALSE, NULL);
		$this->registerArgument('maxWidth', 'string', 'Maximum width of the image. Numeric value in pixels or simple calculations. See imgResource.width for possible options.', FALSE, NULL);
		$this->registerArgument('maxHeight', 'string', 'Maximum height of the image. Numeric value in pixels or simple calculations. See imgResource.width for possible options.', FALSE, NULL);
	}

	/**
	 * @param array $files
	 * @param boolean $onlyProperties
	 * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception
	 * @return array|NULL
	 */
	public function preprocessImages($files, $onlyProperties = FALSE) {
		if (TRUE === empty($files)) {
			return NULL;
		}

		if ('BE' === TYPO3_MODE) {
			$this->simulateFrontendEnvironment();
		}

		$setup = array(
			'width' => $this->arguments['width'],
			'height' => $this->arguments['height'],
			'minW' => $this->arguments['minWidth'],
			'minH' => $this->arguments['minHeight'],
			'maxW' => $this->arguments['maxWidth'],
			'maxH' => $this->arguments['maxHeight'],
			'treatIdAsReference' => FALSE
		);

		$images = array();

		foreach ($files as $file) {
			$imageInfo = $this->contentObject->getImgResource($file->getUid(), $setup);

			$GLOBALS['TSFE']->lastImageInfo = $imageInfo;
			if (FALSE === is_array($imageInfo)) {
				throw new Exception('Could not get image resource for "' . htmlspecialchars($file->getCombinedIdentifier()) . '".', 1253191060);
			}

			$imageInfo[3] = GeneralUtility::png_to_gif_by_imagemagick($imageInfo[3]);
			$GLOBALS['TSFE']->imagesOnPage[] = $imageInfo[3];
			$imageSource = $GLOBALS['TSFE']->absRefPrefix . GeneralUtility::rawUrlEncodeFP($imageInfo[3]);

			if (TRUE === $onlyProperties) {
				$file = ResourceUtility::getFileArray($file);
			}

			$images[] = array(
				'info' => $imageInfo,
				'source' => $imageSource,
				'file' => $file
			);
		}

		if ('BE' === TYPO3_MODE) {
			$this->resetFrontendEnvironment();
		}

		return $images;
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
		$typoScriptSetup = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$GLOBALS['TSFE'] = new \stdClass();
		$template = GeneralUtility::makeInstance('TYPO3\CMS\Core\TypoScript\TemplateService');
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

	/**
	 * Turns a relative source URI into an absolute URL
	 * if required
	 *
	 * @param string $source
	 * @return string
	 */
	public function preprocessSourceUri($source) {
		if (FALSE === empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['settings.']['prependPath'])) {
			$source = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['settings.']['prependPath'] . $source;
		} elseif ('BE' === TYPO3_MODE || FALSE === (boolean) $this->arguments['relative']) {
			$source = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . $source;
		}
		return $source;
	}

}
