<?php
namespace FluidTYPO3\Vhs\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class PageRendererTrait
 *
 * Trait implemented by ViewHelpers which require access
 * to PageRenderer
 *
 */
trait PageRendererTrait
{

    /**
     * Provides a shared (singleton) instance of PageRenderer
     *
     * @return \TYPO3\CMS\Core\Page\PageRenderer
     */
    protected static function getPageRenderer()
    {
        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
    }
}
