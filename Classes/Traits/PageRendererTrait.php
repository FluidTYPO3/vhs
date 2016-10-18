<?php
namespace FluidTYPO3\Vhs\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
     * @return PageRenderer
     */
    protected static function getPageRenderer()
    {
        return GeneralUtility::makeInstance(PageRenderer::class);
    }
}
