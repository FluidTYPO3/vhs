<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Core\ViewHelper\AbstractViewHelper;
use FluidTYPO3\Vhs\Traits\PageRendererTrait;
use FluidTYPO3\Vhs\Utility\ContextUtility;

/**
 * ViewHelper used to place header blocks in document footer
 */
class FooterViewHelper extends AbstractViewHelper
{
    use PageRendererTrait;

    public function render(): string
    {
        if (ContextUtility::isBackend()) {
            return '';
        }
        /** @var string|null $content */
        $content = $this->renderChildren();
        static::getPageRenderer()->addFooterData((string) $content);
        return '';
    }
}
