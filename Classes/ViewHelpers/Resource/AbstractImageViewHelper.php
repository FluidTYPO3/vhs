<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ContextUtility;
use FluidTYPO3\Vhs\Utility\CoreUtility;
use FluidTYPO3\Vhs\Utility\FrontendSimulationUtility;
use FluidTYPO3\Vhs\Utility\ResourceUtility;
use TYPO3\CMS\Core\TypoScript\TemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

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
     * @var string|false
     */
    protected $workingDirectoryBackup;

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var ContentObjectRenderer
     */
    protected $contentObject;

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
        /** @var ContentObjectRenderer $contentObject */
        $contentObject = $this->configurationManager->getContentObject();
        $this->contentObject = $contentObject;
    }

    public function initializeArguments(): void
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
            'for possible options.'
        );
        $this->registerArgument(
            'height',
            'string',
            'Height of the image. Numeric value in pixels or simple calculations. See imgResource.width for ' .
            'possible options.'
        );
        $this->registerArgument(
            'minWidth',
            'string',
            'Minimum width of the image. Numeric value in pixels or simple calculations. See imgResource.width ' .
            'for possible options.'
        );
        $this->registerArgument(
            'minHeight',
            'string',
            'Minimum height of the image. Numeric value in pixels or simple calculations. ' .
            'See imgResource.width for possible options.'
        );
        $this->registerArgument(
            'maxWidth',
            'string',
            'Maximum width of the image. Numeric value in pixels or simple calculations. ' .
            'See imgResource.width for possible options.'
        );
        $this->registerArgument(
            'maxHeight',
            'string',
            'Maximum height of the image. Numeric value in pixels or simple calculations. ' .
            'See imgResource.width for possible options.'
        );
    }

    public function preprocessImages(array $files, bool $onlyProperties = false): ?array
    {
        if (empty($files)) {
            return null;
        }

        $tsfeBackup = FrontendSimulationUtility::simulateFrontendEnvironment();

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

            if (!is_array($imageInfo)) {
                throw new Exception(
                    'Could not get image resource for "' . htmlspecialchars($file->getCombinedIdentifier()) . '".',
                    1253191060
                );
            }

            $GLOBALS['TSFE']->lastImageInfo = $imageInfo;
            $GLOBALS['TSFE']->imagesOnPage[] = $imageInfo[3];

            if (GeneralUtility::isValidUrl($imageInfo[3])) {
                $imageSource = $imageInfo[3];
            } else {
                $imageSource = $GLOBALS['TSFE']->absRefPrefix . str_replace('%2F', '/', rawurlencode($imageInfo[3]));
            }

            if ($onlyProperties) {
                $file = ResourceUtility::getFileArray($file);
            }

            $images[] = [
                'info' => $imageInfo,
                'source' => $imageSource,
                'file' => $file
            ];
        }

        FrontendSimulationUtility::resetFrontendEnvironment($tsfeBackup);

        return $images;
    }

    /**
     * Prepares $GLOBALS['TSFE'] for Backend mode
     * This somewhat hacky work around is currently needed because the getImgResource() function of
     * \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer relies on those variables to be set.
     */
    protected function simulateFrontendEnvironment(): void
    {
        $this->tsfeBackup = $GLOBALS['TSFE'] ?? null;
        $this->workingDirectoryBackup = getcwd();
        chdir(CoreUtility::getSitePath());
        $typoScriptSetup = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );
        $GLOBALS['TSFE'] = new \stdClass();
        /** @var TemplateService $template */
        $template = GeneralUtility::makeInstance(TemplateService::class);
        $template->tt_track = false;
        if (property_exists($template, 'getFileName_backPath')) {
            $template->getFileName_backPath = CoreUtility::getSitePath();
        }
        $GLOBALS['TSFE']->tmpl = $template;
        $GLOBALS['TSFE']->tmpl->setup = $typoScriptSetup;
        $GLOBALS['TSFE']->config = $typoScriptSetup;
    }

    /**
     * Resets $GLOBALS['TSFE'] if it was previously changed by simulateFrontendEnvironment()
     *
     * @see simulateFrontendEnvironment()
     */
    protected function resetFrontendEnvironment(): void
    {
        $GLOBALS['TSFE'] = $this->tsfeBackup;
        if ($this->workingDirectoryBackup !== false) {
            chdir($this->workingDirectoryBackup);
        }
    }

    /**
     * Turns a relative source URI into an absolute URL
     * if required.
     */
    public function preprocessSourceUri(string $source): string
    {
        if (!empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['settings.']['prependPath'])) {
            $source = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['settings.']['prependPath'] . $source;
        } elseif (ContextUtility::isBackend() || !$this->arguments['relative']) {
            /** @var string $siteUrl */
            $siteUrl = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
            $source = $siteUrl . $source;
        }
        return $source;
    }
}
