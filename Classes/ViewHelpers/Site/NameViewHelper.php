<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Site;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\CompileWithRenderStatic;
use FluidTYPO3\Vhs\Utility\RequestResolver;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use FluidTYPO3\Vhs\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Site: Name
 *
 * Returns the site name as specified in `$TYPO3_CONF_VARS`.
 */
class NameViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        $request = RequestResolver::tryResolveRequestFromRenderingContext($renderingContext, false);
        if ($request !== null) {
            $language = $request->getAttribute('language');
            if ($language instanceof SiteLanguage && $language->getWebsiteTitle() !== '') {
                return $language->getWebsiteTitle();
            }

            $site = $request->getAttribute('site');
            if ($site instanceof Site) {
                $settings = $site->getSettings();
                $settingsWebsiteTitle = $settings->get('websiteTitle');
                if (is_string($settingsWebsiteTitle) && $settingsWebsiteTitle !== '') {
                    return $settingsWebsiteTitle;
                }

                try {
                    $siteWebsiteTitle = $site->getAttribute('websiteTitle');
                    if (is_string($siteWebsiteTitle) && $siteWebsiteTitle !== '') {
                        return $siteWebsiteTitle;
                    }
                } catch (\InvalidArgumentException) {
                }
            }
        }

        return $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] ?? 'Unknown TYPO3 site';
    }
}
