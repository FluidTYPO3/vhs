<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class ContentObjectFetcher
{
    public static function resolve(?ConfigurationManagerInterface $configurationManager = null): ?ContentObjectRenderer
    {
        $contentObject = null;
        $request = ($configurationManager !== null && method_exists($configurationManager, 'getRequest')
            ? $configurationManager->getRequest()
            : ($GLOBALS['TYPO3_REQUEST'] ?? null)) ?? $GLOBALS['TYPO3_REQUEST'] ?? null;

        if ($request) {
            $contentObject = static::resolveFromRequest($request);
        }

        if ($contentObject === null
            && $configurationManager !== null
            && method_exists($configurationManager, 'getContentObject')
        ) {
            $contentObject = $configurationManager->getContentObject();
        }

        return $contentObject;
    }

    protected static function resolveFromRequest(ServerRequestInterface $request): ?ContentObjectRenderer
    {
        if (($cObject = $request->getAttribute('currentContentObject')) instanceof ContentObjectRenderer) {
            return $cObject;
        }
        /** @var TypoScriptFrontendController $controller */
        $controller = $request->getAttribute('frontend.controller');
        return $controller instanceof TypoScriptFrontendController ? $controller->cObj : null;
    }
}
