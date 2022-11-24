<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Returns a full, absolute URL to this page with all arguments.
 */
class AbsoluteUrlViewHelper extends AbstractViewHelper
{

    use CompileWithRenderStatic;

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
        /** @var string $url */
        $url = GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');
        /** @var string $siteUrl */
        $siteUrl = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
        if (0 !== strpos($url, $siteUrl)) {
            $url = $siteUrl . $url;
        }
        return $url;
    }
}
