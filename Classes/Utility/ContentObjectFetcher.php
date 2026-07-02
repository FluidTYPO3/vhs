<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class ContentObjectFetcher
{
    public static function resolve(?ConfigurationManagerInterface $configurationManager = null): ContentObjectRenderer
    {
        $contentObject = null;
        $request = ($configurationManager !== null && method_exists($configurationManager, 'getRequest')
            ? $configurationManager->getRequest()
            : ($GLOBALS['TYPO3_REQUEST'] ?? null)) ?? $GLOBALS['TYPO3_REQUEST'] ?? null;

        if ($request instanceof ServerRequestInterface) {
            /** @var ContentObjectRenderer|null $contentObject */
            $contentObject = $request->getAttribute('currentContentObject');
        }

        if ($contentObject === null && VersionUtility::isCoreBelow14()) {
            $controller = RequestResolver::getTypoScriptFrontendController();
            $contentObject = $controller?->cObj ?? null;
        }

        if ($contentObject === null
            && $configurationManager !== null
            && method_exists($configurationManager, 'getContentObject')
        ) {
            $contentObject = $configurationManager->getContentObject();
        }

        if (!$contentObject) {
            /** @var ContentObjectRenderer $contentObject */
            $contentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        }

        return $contentObject;
    }
}
