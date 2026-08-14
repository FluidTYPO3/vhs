<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Cache\CacheInstruction;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageInformation;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Core\Http\Uri;

class RequestResolver
{
    public static function resolveRequestFromRenderingContext(
        RenderingContextInterface $renderingContext
    ): RequestInterface|ServerRequestInterface {
        $request = null;
        if (VersionUtility::isCoreAtLeast13() && $renderingContext->hasAttribute(ServerRequestInterface::class)) {
            /** @var ServerRequestInterface $request */
            $request = $renderingContext->getAttribute(ServerRequestInterface::class);
        } elseif (!VersionUtility::isCoreAtLeast13() && method_exists($renderingContext, 'getRequest')) {
            /** @var RequestInterface $request */
            $request = $renderingContext->getRequest();
        }

        if (!$request) {
            throw new \UnexpectedValueException('Unable to resolve request from RenderingContext', 1673191812);
        }
        return $request;
    }

    public static function resolveControllerNameFromRenderingContext(RenderingContextInterface $context): ?string
    {
        return self::resolveControllerNameFromRequest(self::resolveRequestFromRenderingContext($context));
    }

    public static function resolveControllerNameFromRequest(RequestInterface|ServerRequestInterface $request): ?string
    {
        return self::proxyCall($request, 'getControllerName');
    }

    public static function resolveControllerActionNameFromRenderingContext(RenderingContextInterface $context): ?string
    {
        return self::resolveControllerActionNameFromRequest(self::resolveRequestFromRenderingContext($context));
    }

    public static function resolveControllerActionNameFromRequest(
        RequestInterface|ServerRequestInterface $request
    ): ?string {
        return self::proxyCall($request, 'getControllerActionName');
    }

    public static function resolveControllerExtensionNameFromRenderingContext(
        RenderingContextInterface $context
    ): ?string {
        return self::resolveControllerExtensionNameFromRequest(self::resolveRequestFromRenderingContext($context));
    }

    public static function resolveControllerExtensionNameFromRequest(
        RequestInterface|ServerRequestInterface $request
    ): ?string {
        return self::proxyCall($request, 'getControllerExtensionName');
    }

    public static function resolveControllerObjectNameFromRequest(
        RequestInterface|ServerRequestInterface $request
    ): ?string {
        return self::proxyCall($request, 'getControllerObjectName');
    }

    public static function resolvePluginNameFromRenderingContext(RenderingContextInterface $context): ?string
    {
        return self::resolvePluginNameFromRequest(self::resolveRequestFromRenderingContext($context));
    }

    public static function resolvePluginNameFromRequest(RequestInterface|ServerRequestInterface $request): ?string
    {
        return self::proxyCall($request, 'getPluginName');
    }

    public static function resolveFormatFromRequest(RequestInterface|ServerRequestInterface $request): ?string
    {
        return self::proxyCall($request, 'getFormat');
    }

    public static function getRequest(): ServerRequestInterface
    {
        /** @var ServerRequestInterface|null $request */
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if (!$request) {
            throw new \UnexpectedValueException('Request cannot be resolved', 1777024778);
        }
        return $request;
    }

    public static function getNormalizedParameters(): ?NormalizedParams
    {
        /** @var NormalizedParams|null $params */
        $params = self::getRequest()->getAttribute('normalizedParams');
        return $params;
    }

    public static function getTypoScriptFrontendController(): ?TypoScriptFrontendController
    {
        if (VersionUtility::isCoreAtLeast14()) {
            throw new \RuntimeException('TypoScriptFrontendController cannot be fetched on TYPO3v14', 1782819131);
        }
        $controller = self::getRequest()->getAttribute('frontend.controller');
        if ($controller instanceof TypoScriptFrontendController) {
            return $controller;
        }
        /** @var TypoScriptFrontendController|null $controller */
        $controller = is_object($GLOBALS['TSFE'] ?? null) ? $GLOBALS['TSFE'] : null;
        return $controller;
    }

    public static function getPageInformation(): ?PageInformation
    {
        /** @var PageInformation|null $pageInformation */
        $pageInformation = self::getRequest()->getAttribute('frontend.page.information');
        return $pageInformation;
    }

    public static function getPageUid(): int
    {
        if (($pageInformation = self::getPageInformation()) instanceof PageInformation) {
            return $pageInformation->getId();
        }
        if (($tsfe = self::getTypoScriptFrontendController()) instanceof TypoScriptFrontendController) {
            return $tsfe->id;
        }
        return 0;
    }

    public static function getLanguage(): SiteLanguage
    {
        /** @var SiteLanguage $siteLanguage */
        $siteLanguage = self::getRequest()->getAttribute('language') ?? new SiteLanguage(0, '', new Uri('/'), ['enabled' => true]);
        return $siteLanguage;
    }

    public static function getFrontendUser(): ?FrontendUserAuthentication
    {
        /** @var FrontendUserAuthentication $frontendUser */
        $frontendUser = self::getRequest()->getAttribute('frontend.user');
        return $frontendUser;
    }

    public static function getBackendUser(): ?BackendUserAuthentication
    {
        /** @var BackendUserAuthentication|null $backendUser */
        $backendUser = $GLOBALS['BE_USER'] ?? null;
        return $backendUser;
    }

    public static function isFrontendUserLoggedIn(): bool
    {
        $userAuthentication = self::getFrontendUser();
        return $userAuthentication !== null && $userAuthentication->user !== null;
    }

    public static function isPreview(): bool
    {
        /** @var Context $context */
        $context = GeneralUtility::makeInstance(Context::class);
        return $context->hasAspect('frontend.preview')
            && $context->getPropertyFromAspect('frontend.preview', 'isPreview');
    }

    public static function getWorkspaceId(): ?int
    {
        /** @var Context $context */
        $context = GeneralUtility::makeInstance(Context::class);
        $workspaceId = $context->getPropertyFromAspect('workspace', 'id', 0) ?: null;
        if (!is_numeric($workspaceId)) {
            return null;
        }
        $workspaceId = (int) $workspaceId;
        return $workspaceId > 0 ? $workspaceId : null;
    }

    public static function getFrontendTypoScriptSetup(): array
    {
        $typoScript = self::getRequestOrNull()?->getAttribute('frontend.typoscript');
        if (is_object($typoScript) && method_exists($typoScript, 'getSetupArray')) {
            $setup = $typoScript->getSetupArray();
            return is_array($setup) ? $setup : [];
        }

        return self::getTypoScriptFrontendController()?->tmpl->setup ?? [];
    }

    public static function getStaticPrefix(): string
    {
        return (string) (self::getFrontendTypoScriptSetup()['plugin.']['tx_vhs.']['settings.']['prependPath'] ?? '');
    }

    public static function getFrontendUrlPrefix(): string
    {
        $setup = self::getFrontendTypoScriptSetup();
        $params = self::getRequestOrNull()?->getAttribute('normalizedParams');
        $params = $params instanceof NormalizedParams ? $params : null;

        if (($setup['config.']['forceAbsoluteUrls'] ?? false) && $params) {
            return $params->getSiteUrl();
        }

        $absRefPrefix = trim((string) ($setup['config.']['absRefPrefix'] ?? ''));
        if ($absRefPrefix === 'auto' && $params) {
            return $params->getSitePath();
        }
        if ($absRefPrefix !== '') {
            return $absRefPrefix;
        }

        return self::getTypoScriptFrontendController()?->absRefPrefix ?? '';
    }

    public static function disableFrontendCache(string $reason): void
    {
        $request = self::getRequestOrNull();
        $instruction = $request?->getAttribute('frontend.cache.instruction');
        if ($instruction instanceof CacheInstruction) {
            $instruction->disableCache($reason);
            return;
        }

        $controller = self::getTypoScriptFrontendController();
        if ($controller instanceof TypoScriptFrontendController) {
            /** @phpstan-ignore-next-line Legacy frontend controller fallback exposes dynamic public properties. */
            $controller->no_cache = true;
        }
    }

    private static function proxyCall(RequestInterface|ServerRequestInterface $request, string $method): ?string
    {
        if ($request instanceof RequestInterface) {
            return $request->{$method}();
        }
        if (($parameters = $request->getAttribute('extbase')) instanceof ExtbaseRequestParameters) {
            return $parameters->{$method}();
        }
        return null;
    }

    private static function getRequestOrNull(): ?ServerRequestInterface
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        return $request instanceof ServerRequestInterface ? $request : null;
    }
}
