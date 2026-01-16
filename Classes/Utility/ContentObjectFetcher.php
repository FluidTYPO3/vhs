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
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class ContentObjectFetcher
{
    public static function resolve(
        ?ConfigurationManagerInterface $configurationManager = null,
        ?RenderingContextInterface $renderingContext = null
    ): ?ContentObjectRenderer {

        $contentObject = null;
        $request = static::getRequest($configurationManager, $renderingContext);
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

    protected static function getRequest(
        ?ConfigurationManagerInterface $configurationManager = null,
        ?RenderingContextInterface $renderingContext = null
    ): ?ServerRequestInterface {

        if ($renderingContext !== null) {
            //TYPO3 v13+
            if (method_exists($renderingContext, 'getAttribute')
                && method_exists($renderingContext, 'hasAttribute')
                && $renderingContext->hasAttribute(ServerRequestInterface::class)
            ) {
                return $renderingContext->getAttribute(ServerRequestInterface::class);
            }

            //TYPO3 v12
            if (method_exists($renderingContext, 'getRequest')) {
                return $renderingContext->getRequest();
            }
        }

        if ($configurationManager !== null) {
            if (method_exists($configurationManager, 'getRequest')) {
                return $configurationManager->getRequest();
            }
        }

        return $GLOBALS['TYPO3_REQUEST'] ?? null;
    }
}
