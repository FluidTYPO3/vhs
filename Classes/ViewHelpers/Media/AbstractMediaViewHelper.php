<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Base class for media related view helpers.
 */
abstract class AbstractMediaViewHelper extends AbstractTagBasedViewHelper
{

    /**
     *
     * @var string
     */
    protected $mediaSource;

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
     * if required
     *
     * @param string $src
     * @param array $arguments
     * @return string
     */
    public static function preprocessSourceUri($src, array $arguments)
    {
        $src = $GLOBALS['TSFE']->absRefPrefix . str_replace('%2F', '/', rawurlencode($src));
        if (false === empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['settings.']['prependPath'])) {
            $src = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['settings.']['prependPath'] . $src;
        } elseif ('BE' === TYPO3_MODE || false === (boolean) $arguments['relative']) {
            $src = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . ltrim($src, '/');
        }
        return $src;
    }

    /**
     * Returns an array of sources resolved from src argument
     * which can be either an array, CSV or implement Traversable
     * to be consumed by ViewHelpers handling multiple sources.
     *
     * @param array $arguments
     * @return array
     */
    public static function getSourcesFromArgument(array $arguments)
    {
        $src = $arguments['src'];
        if ($src instanceof \Traversable) {
            $src = iterator_to_array($src);
        } elseif (true === is_string($src)) {
            $src = GeneralUtility::trimExplode(',', $src, true);
        }
        return $src;
    }
}
