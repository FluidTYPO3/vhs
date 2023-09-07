<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ContextUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Base class for media related view helpers.
 */
abstract class AbstractMediaViewHelper extends AbstractTagBasedViewHelper
{
    protected string $mediaSource = '';

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument(
            'src',
            'mixed',
            'Path to the media resource(s). Can contain single or multiple paths for videos/audio (either CSV, ' .
            'array or implementing Traversable).',
            true
        );
        $this->registerArgument(
            'relative',
            'boolean',
            'If FALSE media URIs are rendered absolute. URIs in backend mode are always absolute.',
            false,
            true
        );
    }

    /**
     * Turns a relative source URI into an absolute URL
     * if required.
     */
    public static function preprocessSourceUri(string $src, array $arguments): string
    {
        $src = str_replace('%2F', '/', rawurlencode($src));
        if (substr($src, 0, 1) !== '/' && substr($src, 0, 4) !== 'http') {
            $src = $GLOBALS['TSFE']->absRefPrefix . $src;
        }
        if (!empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['settings.']['prependPath'])) {
            $src = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['settings.']['prependPath'] . $src;
        } elseif (ContextUtility::isBackend() || !$arguments['relative']) {
            /** @var string $siteUrl */
            $siteUrl = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
            $src = $siteUrl . ltrim($src, '/');
        }
        if (empty($src)) {
            // Do not pass an empty $src to PathUtility, it requires non-empty strings on 10.4.
            return '';
        }
        return PathUtility::getAbsoluteWebPath($src);
    }

    /**
     * Returns an array of sources resolved from src argument
     * which can be either an array, CSV or implement Traversable
     * to be consumed by ViewHelpers handling multiple sources.
     */
    public static function getSourcesFromArgument(array $arguments): array
    {
        $src = $arguments['src'];
        if ($src instanceof \Traversable) {
            $src = iterator_to_array($src);
        } elseif (is_string($src)) {
            $src = GeneralUtility::trimExplode(',', $src, true);
        }
        return $src;
    }
}
