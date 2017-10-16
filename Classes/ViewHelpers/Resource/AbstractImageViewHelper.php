<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ResourceUtility;
use TYPO3\CMS\Core\Imaging\GraphicalFunctions;
use TYPO3\CMS\Core\TypoScript\TemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * Base class for image related view helpers adapted from FLUID
 * original image viewhelper.
 */
abstract class AbstractImageViewHelper extends AbstractResourceViewHelper
{

    /**
     * @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController contains a backup of
     * the current $GLOBALS['TSFE'] if used in BE mode
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
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
        $this->contentObject = $this->configurationManager->getContentObject();
    }

    /**
     * Initialize arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument(
            'relative',
            'boolean',
            'If FALSE resource URIs are rendered absolute. URIs in backend mode are always absolute.',
            false,
            true
        );
        $this->registerArgument(
            'width',
            'string',
            'Width of the image. Numeric value in pixels or simple calculations. See imgResource.width ' .
            'for possible options.',
            false,
            null
        );
        $this->registerArgument(
            'height',
            'string',
            'Height of the image. Numeric value in pixels or simple calculations. See imgResource.width for ' .
            'possible options.',
            false,
            null
        );
        $this->registerArgument(
            'minWidth',
            'string',
            'Minimum width of the image. Numeric value in pixels or simple calculations. See imgResource.width ' .
            'for possible options.',
            false,
            null
        );
        $this->registerArgument(
            'minHeight',
            'string',
            'Minimum height of the image. Numeric value in pixels or simple calculations. ' .
            'See imgResource.width for possible options.',
            false,
            null
        );
        $this->registerArgument(
            'maxWidth',
            'string',
            'Maximum width of the image. Numeric value in pixels or simple calculations. ' .
            'See imgResource.width for possible options.',
            false,
            null
        );
        $this->registerArgument(
            'maxHeight',
            'string',
            'Maximum height of the image. Numeric value in pixels or simple calculations. ' .
            'See imgResource.width for possible options.',
            false,
            null
        );
    }

    /**
     * @param array $files
     * @param boolean $onlyProperties
     * @throws Exception
     * @return array|NULL
     */
    public function preprocessImages($files, $onlyProperties = false)
    {
        if (true === empty($files)) {
            return null;
        }

        if ('BE' === TYPO3_MODE) {
            $this->simulateFrontendEnvironment();
        }

        $setup = [
            'width' => $this->arguments['width'],
            'height' => $this->arguments['height'],
            'minW' => $this->arguments['minWidth'],
            'minH' => $this->arguments['minHeight'],
            'maxW' => $this->arguments['maxWidth'],
            'maxH' => $this->arguments['maxHeight'],
            'treatIdAsReference' => false
        ];

        $images = [];

        foreach ($files as $file) {
            $imageInfo = $this->contentObject->getImgResource($file->getUid(), $setup);

            if (false === is_array($imageInfo)) {
                throw new Exception(
                    'Could not get image resource for "' . htmlspecialchars($file->getCombinedIdentifier()) . '".',
                    1253191060
                );
            }

            $GLOBALS['TSFE']->lastImageInfo = $imageInfo;
            $GLOBALS['TSFE']->imagesOnPage[] = $imageInfo[3];

            if (true === GeneralUtility::isValidUrl($imageInfo[3])) {
                $imageSource = $imageInfo[3];
            } else {
                $imageSource = $GLOBALS['TSFE']->absRefPrefix . str_replace('%2F', '/', rawurlencode($imageInfo[3]));
            }

            if (true === $onlyProperties) {
                $file = ResourceUtility::getFileArray($file);
            }

            $images[] = [
                'info' => $imageInfo,
                'source' => $imageSource,
                'file' => $file
            ];
        }

        if ('BE' === TYPO3_MODE) {
            $this->resetFrontendEnvironment();
        }

        return $images;
    }

    /**
     * Prepares $GLOBALS['TSFE'] for Backend mode
     * This somewhat hacky work around is currently needed because the getImgResource() function of
     * \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer relies on those variables to be set
     *
     * @return void
     */
    protected function simulateFrontendEnvironment()
    {
        $this->tsfeBackup = isset($GLOBALS['TSFE']) ? $GLOBALS['TSFE'] : null;
        $this->workingDirectoryBackup = getcwd();
        chdir(constant('PATH_site'));
        $typoScriptSetup = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );
        $GLOBALS['TSFE'] = new \stdClass();
        $template = GeneralUtility::makeInstance(TemplateService::class);
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
    protected function resetFrontendEnvironment()
    {
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
    public function preprocessSourceUri($source)
    {
        if (false === empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['settings.']['prependPath'])) {
            $source = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['settings.']['prependPath'] . $source;
        } elseif ('BE' === TYPO3_MODE || false === (boolean) $this->arguments['relative']) {
            $source = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . $source;
        }
        return $source;
    }
}
