<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Core\ViewHelper\AbstractViewHelper;
use FluidTYPO3\Vhs\Utility\RequestResolver;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Returns a full, absolute URL to this page with all arguments.
 */
class AbsoluteUrlViewHelper extends AbstractViewHelper
{
    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $url = (string) RequestResolver::getRequest()->getUri();
        $siteUrl = RequestResolver::getFrontendUrlPrefix();
        if (!str_starts_with($url, $siteUrl)) {
            $url = $siteUrl . $url;
        }
        return $url;
    }
}
