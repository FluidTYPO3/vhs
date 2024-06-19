<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ContextUtility;
use FluidTYPO3\Vhs\Utility\FrontendSimulationUtility;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Used in conjuntion with the `v:media.PictureViewHelper`.
 * Please take a look at the `v:media.PictureViewHelper` documentation for more
 * information.
 */
class SourceViewHelper extends AbstractTagBasedViewHelper
{
    const SCOPE = 'FluidTYPO3\Vhs\ViewHelpers\Media\PictureViewHelper';
    const SCOPE_VARIABLE_SRC = 'src';
    const SCOPE_VARIABLE_ID = 'treatIdAsReference';
    const SCOPE_VARIABLE_DEFAULT_SOURCE = 'default-source';

    /**
     * name of the tag to be created by this view helper
     *
     * @var string
     * @api
     */
    protected $tagName = 'source';

    /**
     * @var ContentObjectRenderer
     */
    protected $contentObject;

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

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
        $this->registerUniversalTagAttributes();
        $this->registerArgument('media', 'string', 'Media query for which breakpoint this sources applies');
        $this->registerArgument(
            'width',
            'string',
            'Width of the image. This can be a numeric value representing the fixed width of the image in pixels. ' .
            'But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width ' .
            'for possible options.'
        );
        $this->registerArgument(
            'height',
            'string',
            'Height of the image. This can be a numeric value representing the fixed height of the image in pixels. ' .
            'But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width ' .
            'for possible options.'
        );
        $this->registerArgument('maxW', 'integer', 'Maximum Width of the image. (no upscaling)');
        $this->registerArgument('maxH', 'integer', 'Maximum Height of the image. (no upscaling)');
        $this->registerArgument('minW', 'integer', 'Minimum Width of the image.');
        $this->registerArgument('minH', 'integer', 'Minimum Height of the image.');
        $this->registerArgument(
            'format',
            'string',
            'Format of the processed file - also determines the target file format. If blank, TYPO3/IM/GM default ' .
            'is taken into account.'
        );
        $this->registerArgument(
            'quality',
            'integer',
            'Quality of the processed image. If blank/not present falls back to the default quality defined ' .
            'in install tool.',
            false,
            $GLOBALS['TYPO3_CONF_VARS']['GFX']['jpg_quality'] ?? 90
        );
        $this->registerArgument('relative', 'boolean', 'Produce a relative URL instead of absolute', false, false);
    }

    /**
     * Render method
     *
     * @return string
     */
    public function render()
    {
        $viewHelperVariableContainer = $this->renderingContext->getViewHelperVariableContainer();
        /** @var FileReference|string $imageSource */
        $imageSource = $viewHelperVariableContainer->get(static::SCOPE, static::SCOPE_VARIABLE_SRC);
        $treatIdAsRerefence = $viewHelperVariableContainer->get(static::SCOPE, static::SCOPE_VARIABLE_ID);

        $tsfeBackup = FrontendSimulationUtility::simulateFrontendEnvironment();

        $setup = [
            'width' => $this->arguments['width'],
            'height' => $this->arguments['height'],
            'minW' => $this->arguments['minW'],
            'minH' => $this->arguments['minH'],
            'maxW' => $this->arguments['maxW'],
            'maxH' => $this->arguments['maxH'],
            'treatIdAsReference' => $treatIdAsRerefence,
            'params' => '',
        ];
        /** @var int $quality */
        $quality = $this->arguments['quality'];
        /** @var string $format */
        $format = $this->arguments['format'];

        if (!empty($format)) {
            $setup['ext'] = $format;
        }
        if (0 < intval($quality)) {
            $quality = MathUtility::forceIntegerInRange($quality, 10, 100, 75);
            $setup['params'] .= ' -quality ' . $quality;
        }

        if (is_string($imageSource) && ContextUtility::isBackend() && '../' === mb_substr($imageSource, 0, 3)) {
            $imageSource = mb_substr($imageSource, 3);
        }
        $result = $this->contentObject->getImgResource($imageSource, $setup);

        FrontendSimulationUtility::resetFrontendEnvironment($tsfeBackup);

        if ($result['processedFile'] ?? false) {
            $imageUrl = $result['processedFile']->getPublicUrl();
        } else {
            $imageUrl = $result[3] ?? '';
        }
        $src = $this->preprocessSourceUri(rawurldecode($imageUrl));

        /** @var string|null $media */
        $media = $this->arguments['media'];

        if (null === $media) {
            $viewHelperVariableContainer->addOrUpdate(static::SCOPE, static::SCOPE_VARIABLE_DEFAULT_SOURCE, $src);
        } else {
            $this->tag->addAttribute('media', $media);
        }

        $this->tag->addAttribute('srcset', $src);
        return $this->tag->render();
    }

    /**
     * Turns a relative source URI into an absolute URL
     * if required.
     */
    public function preprocessSourceUri(string $src): string
    {
        if (!empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['settings.']['prependPath'])) {
            $src = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['settings.']['prependPath'] . $src;
        } elseif (ContextUtility::isBackend() || !$this->arguments['relative']) {
            if (GeneralUtility::isValidUrl($src)) {
                $src = ltrim($src, '/');
            } elseif (ContextUtility::isFrontend()) {
                $src = $GLOBALS['TSFE']->absRefPrefix . ltrim($src, '/');
            } else {
                /** @var string $siteUrl */
                $siteUrl = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
                $src = $siteUrl . ltrim($src, '/');
            }
        }
        return $src;
    }
}
