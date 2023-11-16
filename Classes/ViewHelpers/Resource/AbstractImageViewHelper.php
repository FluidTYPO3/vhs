<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ContextUtility;
use FluidTYPO3\Vhs\Utility\FrontendSimulationUtility;
use FluidTYPO3\Vhs\Utility\ResourceUtility;
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
        $this->registerArgument(
            'graceful',
            'bool',
            'Set to TRUE to ignore files that cannot be loaded. Default behavior is to throw an Exception.',
            false,
            false
        );
    }

    public function preprocessImages(array $files, bool $onlyProperties = false): ?array
    {
        if (empty($files)) {
            return null;
        }

        $tsfeBackup = FrontendSimulationUtility::simulateFrontendEnvironment();

        $setup = [
            'width' => $this->arguments['width'] ?? null,
            'height' => $this->arguments['height'] ?? null,
            'minW' => $this->arguments['minWidth'] ?? null,
            'minH' => $this->arguments['minHeight'] ?? null,
            'maxW' => $this->arguments['maxWidth'] ?? null,
            'maxH' => $this->arguments['maxHeight'] ?? null,
            'treatIdAsReference' => false
        ];

        $images = [];

        foreach ($files as $file) {
            $imageInfo = $this->contentObject->getImgResource($file->getUid(), $setup);

            if (!is_array($imageInfo)) {
                if ($this->arguments['graceful'] ?? false) {
                    continue;
                }
                throw new Exception(
                    'Could not get image resource for "' . htmlspecialchars($file->getCombinedIdentifier()) . '".',
                    1253191060
                );
            }

            if (property_exists($GLOBALS['TSFE'], 'imagesOnPage')) {
                $GLOBALS['TSFE']->lastImageInfo = $imageInfo;
                $GLOBALS['TSFE']->imagesOnPage[] = $imageInfo[3];
            }

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
