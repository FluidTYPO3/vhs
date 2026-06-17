<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class RequestResolver
{
    public static function resolveRequestFromRenderingContext(
        ?RenderingContextInterface $renderingContext,
        bool $allowGlobalFallback = true
    ): ServerRequestInterface {
        $request = self::tryResolveRequestFromRenderingContext($renderingContext, $allowGlobalFallback);
        if (!$request instanceof ServerRequestInterface) {
            throw new \UnexpectedValueException('Unable to resolve request from RenderingContext', 1673191812);
        }
        return $request;
    }

    public static function tryResolveRequestFromRenderingContext(
        ?RenderingContextInterface $renderingContext,
        bool $allowGlobalFallback = true
    ): ?ServerRequestInterface {
        $request = null;
        if ($renderingContext instanceof RenderingContextInterface) {
            if (method_exists($renderingContext, 'getRequest')) {
                $request = $renderingContext->getRequest();
            } elseif (method_exists($renderingContext, 'hasAttribute')
                && $renderingContext->hasAttribute(ServerRequestInterface::class)
            ) {
                $request = $renderingContext->getAttribute(ServerRequestInterface::class);
            } elseif (method_exists($renderingContext, 'getControllerContext')) {
                $request = $renderingContext->getControllerContext()->getRequest();
            }
        }

        if ($allowGlobalFallback && null === $request && isset($GLOBALS['TYPO3_REQUEST'])) {
            $request = $GLOBALS['TYPO3_REQUEST'];
        }

        return $request instanceof ServerRequestInterface ? $request : null;
    }

    public static function resolveExtbaseRequestFromRenderingContext(
        ?RenderingContextInterface $renderingContext
    ): ?RequestInterface {
        $request = self::resolveRequestFromRenderingContext($renderingContext);
        if ($request instanceof RequestInterface) {
            return $request;
        }
        if ($request instanceof ServerRequestInterface
            && $request->getAttribute('extbase') instanceof ExtbaseRequestParameters
        ) {
            return new Request($request);
        }

        return null;
    }

    public static function resolveControllerNameFromRenderingContext(RenderingContextInterface $context): ?string
    {
        return self::resolveControllerNameFromRequest(self::resolveRequestFromRenderingContext($context));
    }

    /**
     * @param RequestInterface|ServerRequestInterface $request
     */
    public static function resolveControllerNameFromRequest(RequestInterface|ServerRequestInterface $request): ?string
    {
        return self::proxyCall($request, 'getControllerName');
    }

    public static function resolveControllerActionNameFromRenderingContext(RenderingContextInterface $context): ?string
    {
        return self::resolveControllerActionNameFromRequest(self::resolveRequestFromRenderingContext($context));
    }

    /**
     * @param RequestInterface|ServerRequestInterface $request
     */
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

    /**
     * @param RequestInterface|ServerRequestInterface $request
     */
    public static function resolveControllerExtensionNameFromRequest(
        RequestInterface|ServerRequestInterface $request
    ): ?string {
        return self::proxyCall($request, 'getControllerExtensionName');
    }

    public static function resolveControllerObjectNameFromRenderingContext(RenderingContextInterface $context): ?string
    {
        return self::resolveControllerObjectNameFromRequest(self::resolveRequestFromRenderingContext($context));
    }

    /**
     * @param RequestInterface|ServerRequestInterface $request
     */
    public static function resolveControllerObjectNameFromRequest(
        RequestInterface|ServerRequestInterface $request
    ): ?string {
        return self::proxyCall($request, 'getControllerObjectName');
    }

    public static function resolvePluginNameFromRenderingContext(RenderingContextInterface $context): ?string
    {
        return self::resolvePluginNameFromRequest(self::resolveRequestFromRenderingContext($context));
    }

    /**
     * @param RequestInterface|ServerRequestInterface $request
     */
    public static function resolvePluginNameFromRequest(RequestInterface|ServerRequestInterface $request): ?string
    {
        return self::proxyCall($request, 'getPluginName');
    }

    public static function resolveFormatFromRenderingContext(RenderingContextInterface $context): ?string
    {
        return self::resolveFormatFromRequest(self::resolveRequestFromRenderingContext($context));
    }

    /**
     * @param RequestInterface|ServerRequestInterface $request
     */
    public static function resolveFormatFromRequest(RequestInterface|ServerRequestInterface $request): ?string
    {
        return self::proxyCall($request, 'getFormat');
    }

    /**
     * @param RequestInterface|ServerRequestInterface $request
     */
    private static function proxyCall(RequestInterface|ServerRequestInterface $request, string $method): ?string
    {
        if ($request instanceof RequestInterface) {
            return $request->{$method}();
        }
        if (($parameters = $request->getAttribute('extbase')) instanceof ExtbaseRequestParameters) {
            /** @var ExtbaseRequestParameters $parameters */
            return $parameters->{$method}();
        }
        return null;
    }
}
