<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TagViewHelperCompatibility;
use FluidTYPO3\Vhs\Utility\ContextUtility;
use FluidTYPO3\Vhs\Utility\RequestResolver;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\TypoScript\FrontendTypoScript;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Base class for media related view helpers.
 */
abstract class AbstractMediaViewHelper extends AbstractTagBasedViewHelper
{
    use TagViewHelperCompatibility;

    protected string $mediaSource = '';

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument(
            'src',
            'mixed',
            'Path to the media resource(s). Can contain single or multiple paths for videos/audio (either CSV, ' .
            'array or implementing Traversable).',
            true
        );
        $this->registerArgument(
            'relative',
            'boolean',
            'If FALSE media URIs are rendered absolute. URIs in backend mode are always absolute.',
            false,
            true
        );
    }

    /**
     * Turns a relative source URI into an absolute URL
     * if required.
     */
    public static function preprocessSourceUri(
        string $src,
        array $arguments,
        ?ServerRequestInterface $request = null
    ): string {
        $src = str_replace('%2F', '/', rawurlencode($src));
        if (!str_starts_with($src, '/') && !str_starts_with($src, 'http')) {
            $src = static::readFrontendAbsRefPrefix($request) . $src;
        }
        $prependPath = static::readPrependPathFromContext($request);
        if (!empty($prependPath)) {
            $src = $prependPath . $src;
        } elseif (ContextUtility::isBackend() || !$arguments['relative']) {
            if ($request instanceof ServerRequestInterface) {
                $src = static::readSiteUrlFromRequest($request) . ltrim($src, '/');
            }
        }
        if (empty($src)) {
            // Do not pass an empty $src to PathUtility, it requires non-empty strings on 10.4.
            return '';
        }
        return PathUtility::getAbsoluteWebPath($src);
    }

    protected function resolveRequest(): ?ServerRequestInterface
    {
        return RequestResolver::tryResolveRequestFromRenderingContext($this->renderingContext, false);
    }

    protected static function readPrependPathFromContext(?ServerRequestInterface $request): string
    {
        if (!$request instanceof ServerRequestInterface) {
            return '';
        }
        $frontendTypoScript = $request->getAttribute('frontend.typoscript');
        if (!$frontendTypoScript instanceof FrontendTypoScript || !$frontendTypoScript->hasSetup()) {
            return '';
        }
        try {
            $setup = $frontendTypoScript->getSetupArray();
        } catch (\RuntimeException) {
            return '';
        }
        return (string) ($setup['plugin.']['tx_vhs.']['settings.']['prependPath'] ?? '');
    }

    protected static function readFrontendAbsRefPrefix(?ServerRequestInterface $request): string
    {
        if (!$request instanceof ServerRequestInterface) {
            return '';
        }
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '14.0', '<')) {
            return static::readFrontendAbsRefPrefixFromTypoScript($request);
        }

        $frontendUrlPrefixClassName = 'TYPO3\\CMS\\Frontend\\Page\\FrontendUrlPrefix';
        if (!class_exists($frontendUrlPrefixClassName)) {
            return '';
        }
        // @phpstan-ignore-next-line TYPO3 14-only class name, guarded for TYPO3 13.4.
        $frontendUrlPrefix = GeneralUtility::makeInstance($frontendUrlPrefixClassName);
        return method_exists($frontendUrlPrefix, 'getUrlPrefix')
            ? (string) $frontendUrlPrefix->getUrlPrefix($request)
            : '';
    }

    protected static function readFrontendAbsRefPrefixFromTypoScript(ServerRequestInterface $request): string
    {
        $frontendTypoScript = $request->getAttribute('frontend.typoscript');
        if (!$frontendTypoScript instanceof FrontendTypoScript || !$frontendTypoScript->hasSetup()) {
            return '';
        }
        try {
            $setup = $frontendTypoScript->getSetupArray();
        } catch (\RuntimeException) {
            return '';
        }
        return (string) ($setup['config.']['absRefPrefix'] ?? '');
    }

    protected static function readSiteUrlFromRequest(ServerRequestInterface $request): string
    {
        $normalizedParams = $request->getAttribute('normalizedParams');
        if ($normalizedParams instanceof NormalizedParams) {
            return $normalizedParams->getSiteUrl();
        }
        try {
            $uri = $request->getUri();
            $path = (string) $uri->getPath();
            if ('' === $path || '/' === $path) {
                $path = '/';
            }
            $path = rtrim(dirname($path), '/');
            return $uri->withPath($path . '/')->withQuery('')->withFragment('')->__toString();
        } catch (\Throwable) {
        }
        return '';
    }

    /**
     * Returns an array of sources resolved from src argument
     * which can be either an array, CSV or implement Traversable
     * to be consumed by ViewHelpers handling multiple sources.
     */
    public static function getSourcesFromArgument(array $arguments): array
    {
        $src = $arguments['src'];
        if ($src instanceof \Traversable) {
            $src = iterator_to_array($src);
        } elseif (is_string($src)) {
            $src = GeneralUtility::trimExplode(',', $src, true);
        }
        return $src;
    }
}
