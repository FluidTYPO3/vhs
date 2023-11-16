<?php
namespace FluidTYPO3\Vhs\Traits;

use FluidTYPO3\Vhs\Utility\ContextUtility;
use FluidTYPO3\Vhs\Utility\FrontendSimulationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * This trait can be used by viewhelpers that generate image tags
 * to add srcsets based to the imagetag for better responsiveness
 */
trait SourceSetViewHelperTrait
{
    /**
     * Used to attach srcset variants of a given image to the specified tag.
     */
    public function addSourceSet(TagBuilder $tag, string $src): array
    {
        $srcsets = $this->getSourceSetWidths();

        $tsfeBackup = FrontendSimulationUtility::simulateFrontendEnvironment();

        /** @var string|null $format */
        $format = $this->arguments['format'];
        /** @var int $quality */
        $quality = $this->arguments['quality'];
        /** @var string|null $crop */
        $crop = $this->arguments['crop'];
        $treatIdAsReference = (boolean) $this->arguments['treatIdAsReference'];
        if ($treatIdAsReference) {
            /** @var string $src */
            $src = $this->arguments['src'];
        }

        $imageSources = [];
        $srcsetVariants = [];

        foreach ($srcsets as $width) {
            $srcsetVariant = $this->getImgResource($src, $width, $format, $quality, $treatIdAsReference, null, $crop);

            if ($srcsetVariant['processedFile'] ?? false) {
                $imageUrl = $srcsetVariant['processedFile']->getPublicUrl();
            } else {
                $imageUrl = $srcsetVariant[3] ?? '';
            }
            $srcsetVariantSrc = rawurldecode($imageUrl);
            $srcsetVariantSrc = static::preprocessSourceUri(
                str_replace('%2F', '/', rawurlencode($srcsetVariantSrc)),
                $this->arguments
            );

            $imageSources[$srcsetVariant[0]] = [
                'src' => $srcsetVariantSrc,
                'width' => $srcsetVariant[0],
                'height' => $srcsetVariant[1],
            ];
            $srcsetVariants[$srcsetVariant[0]] = $srcsetVariantSrc . ' ' . $srcsetVariant[0] . 'w';
        }

        $tag->addAttribute('srcset', implode(',', $srcsetVariants));

        FrontendSimulationUtility::resetFrontendEnvironment($tsfeBackup);

        return $imageSources;
    }

    /**
     * Generates a copy of a give image with a specific width
     *
     * @param string $src path of the image to convert
     * @param integer $width width to convert the image to
     * @param string $format format of the resulting copy
     * @param integer $quality quality of the resulting copy
     * @param bool $treatIdAsReference given src argument is a sys_file_reference record
     * @param string|null $params additional params for the image rendering
     * @param string|null $crop image editor cropping configuration
     * @return array
     */
    public function getImgResource(
        string $src,
        int $width,
        ?string $format,
        int $quality,
        bool $treatIdAsReference,
        ?string $params = null,
        ?string $crop = null
    ): array {
        $setup = [
            'width' => $width,
            'treatIdAsReference' => $treatIdAsReference,
            'crop' => $crop,
            'params' => $params,
        ];
        if (!empty($format)) {
            $setup['ext'] = $format;
        }
        if (0 < $quality) {
            $quality = MathUtility::forceIntegerInRange($quality, 10, 100, 75);
            $setup['params'] .= ' -quality ' . $quality;
        }

        if (ContextUtility::isBackend() && '../' === substr($src, 0, 3)) {
            $src = substr($src, 3);
        }
        return (array) $this->contentObject->getImgResource($src, $setup);
    }

    /**
     * Returns an array of srcsets based on the mixed ViewHelper
     * input (list, csv, array, iterator).
     */
    public function getSourceSetWidths(): array
    {
        $srcsets = $this->arguments['srcset'];
        if ($srcsets instanceof \Traversable) {
            $srcsets = iterator_to_array($srcsets);
        } elseif (is_string($srcsets)) {
            $srcsets = GeneralUtility::trimExplode(',', $srcsets, true);
        } else {
            $srcsets = (array) $srcsets;
        }
        return $srcsets;
    }
}
