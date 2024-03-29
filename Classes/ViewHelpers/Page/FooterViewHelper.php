<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\PageRendererTrait;
use FluidTYPO3\Vhs\Utility\ContextUtility;
use FluidTYPO3\Vhs\ViewHelpers\Asset\AbstractAssetViewHelper;

/**
 * ViewHelper used to place header blocks in document footer
 */
class FooterViewHelper extends AbstractAssetViewHelper
{
    use PageRendererTrait;

    /**
     * Render method
     *
     * @return void
     */
    public function render()
    {
        if (ContextUtility::isBackend()) {
            return;
        }
        $content = (string) $this->getContent();
        static::getPageRenderer()->addFooterData($content);
    }
}
