<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ContentObjectFetcher;
use FluidTYPO3\Vhs\Utility\ContextUtility;
use FluidTYPO3\Vhs\Utility\FrontendSimulationUtility;
use FluidTYPO3\Vhs\Utility\RequestResolver;
use FluidTYPO3\Vhs\Utility\ResourceUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Imaging\ImageResource;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * Base class for image related view helpers adapted from FLUID
 * original image viewhelper.
 */
abstract class AbstractImageViewHelper extends AbstractTagBasedResourceViewHelper
{
    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument(
            'relative',
            'boolean',
            'If FALSE resource URIs are rendered absolute. URIs in backend mode are always absolute.',
            false,
            true
        );
        $this->registerArgument(
            'width',
            'string',
            'Width of the image. Numeric value in pixels or simple calculations. See imgResource.width ' .
            'for possible options.'
        );
        $this->registerArgument(
            'height',
            'string',
            'Height of the image. Numeric value in pixels or simple calculations. See imgResource.width for ' .
            'possible options.'
        );
        $this->registerArgument(
            'minWidth',
            'string',
            'Minimum width of the image. Numeric value in pixels or simple calculations. See imgResource.width ' .
            'for possible options.'
        );
        $this->registerArgument(
            'minHeight',
            'string',
            'Minimum height of the image. Numeric value in pixels or simple calculations. ' .
            'See imgResource.width for possible options.'
        );
        $this->registerArgument(
            'maxWidth',
            'string',
            'Maximum width of the image. Numeric value in pixels or simple calculations. ' .
            'See imgResource.width for possible options.'
        );
        $this->registerArgument(
            'maxHeight',
            'string',
            'Maximum height of the image. Numeric value in pixels or simple calculations. ' .
            'See imgResource.width for possible options.'
        );
        $this->registerArgument(
            'graceful',
            'bool',
            'Set to TRUE to ignore files that cannot be loaded. Default behavior is to throw an Exception.',
            false,
            false
        );
    }

    public function preprocessImages(array $files, bool $onlyProperties = false): ?array
    {
        if (empty($files)) {
            return null;
        }

        $tsfeBackup = FrontendSimulationUtility::simulateFrontendEnvironment();

        $contentObject = ContentObjectFetcher::resolve($this->configurationManager);
        if ($contentObject === null) {
            throw new Exception(static::class . ' requires a ContentObjectRenderer, none found', 1737807859);
        }

        $setup = [
            'width' => $this->arguments['width'] ?? null,
            'height' => $this->arguments['height'] ?? null,
            'minW' => $this->arguments['minWidth'] ?? null,
            'minH' => $this->arguments['minHeight'] ?? null,
            'maxW' => $this->arguments['maxWidth'] ?? null,
            'maxH' => $this->arguments['maxHeight'] ?? null,
            'treatIdAsReference' => false
        ];

        $images = [];

        foreach ($files as $file) {
            $imageInfo = $contentObject->getImgResource($file->getUid(), $setup);
            if ($imageInfo instanceof ImageResource) {
                $imageInfo = $imageInfo->getLegacyImageResourceInformation();
            }

            if (!is_array($imageInfo)) {
                if ($this->arguments['graceful'] ?? false) {
                    continue;
                }
                throw new Exception(
                    'Could not get image resource for "'
                        . htmlspecialchars((string) $file->getCombinedIdentifier())
                        . '".',
                    1253191060
                );
            }

            $frontendController = $this->resolveFrontendController();
            if ($frontendController !== null && property_exists($frontendController, 'imagesOnPage')) {
                // @phpstan-ignore-next-line
                $frontendController->lastImageInfo = $imageInfo;
                // @phpstan-ignore-next-line
                $frontendController->imagesOnPage[] = $imageInfo[3];
            }

            if (GeneralUtility::isValidUrl($imageInfo[3])) {
                $imageSource = $imageInfo[3];
            } else {
                $imageSource = static::readFrontendAbsRefPrefix($this->resolveRequest())
                    . str_replace('%2F', '/', rawurlencode($imageInfo[3]));
            }

            if ($onlyProperties) {
                $file = ResourceUtility::getFileArray($file);
            }

            $images[] = [
                'info' => $imageInfo,
                'source' => $imageSource,
                'file' => $file
            ];
        }

        FrontendSimulationUtility::resetFrontendEnvironment($tsfeBackup);

        return $images;
    }

    /**
     * Turns a relative source URI into an absolute URL
     * if required.
     */
    public function preprocessSourceUri(string $source): string
    {
        $request = $this->resolveRequest();
        $prependPath = $this->readPrependPathFromContext($request);
        if (!empty($prependPath)) {
            $source = $prependPath . $source;
        } elseif ((ContextUtility::isBackend() || !($this->arguments['relative'] ?? false))
            && $request instanceof ServerRequestInterface
        ) {
            $source = $this->readSiteUrlFromRequest($request) . ltrim($source, '/');
        }
        return $source;
    }

    protected function resolveRequest(): ?ServerRequestInterface
    {
        return RequestResolver::tryResolveRequestFromRenderingContext($this->renderingContext, false);
    }

    protected static function readFrontendAbsRefPrefix(?ServerRequestInterface $request): string
    {
        if (!$request instanceof ServerRequestInterface) {
            return '';
        }
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '14.0', '<')) {
            return static::readFrontendAbsRefPrefixFromTypoScript($request);
        }
        try {
            $frontendUrlPrefixClassName = 'TYPO3\\CMS\\Frontend\\Page\\FrontendUrlPrefix';
            if (!class_exists($frontendUrlPrefixClassName)) {
                return '';
            }
            // @phpstan-ignore-next-line TYPO3 14-only class name, guarded for TYPO3 13.4.
            $frontendUrlPrefix = GeneralUtility::makeInstance($frontendUrlPrefixClassName);
            return method_exists($frontendUrlPrefix, 'getUrlPrefix')
                ? (string) $frontendUrlPrefix->getUrlPrefix($request)
                : '';
        } catch (\Throwable) {
            return '';
        }
    }

    protected static function readFrontendAbsRefPrefixFromTypoScript(ServerRequestInterface $request): string
    {
        $frontendTypoScript = $request->getAttribute('frontend.typoscript');
        if (!is_object($frontendTypoScript) || !method_exists($frontendTypoScript, 'getSetupArray')) {
            return '';
        }
        if (method_exists($frontendTypoScript, 'hasSetup') && !$frontendTypoScript->hasSetup()) {
            return '';
        }
        try {
            $setup = $frontendTypoScript->getSetupArray();
        } catch (\RuntimeException) {
            return '';
        }
        return (string) ($setup['config.']['absRefPrefix'] ?? '');
    }

    protected function readPrependPathFromContext(?ServerRequestInterface $request): string
    {
        if (!$request instanceof ServerRequestInterface) {
            return '';
        }
        $frontendTypoScript = $request->getAttribute('frontend.typoscript');
        if (!is_object($frontendTypoScript) || !method_exists($frontendTypoScript, 'getSetupArray')) {
            return '';
        }
        if (method_exists($frontendTypoScript, 'hasSetup') && !$frontendTypoScript->hasSetup()) {
            return '';
        }
        try {
            $setup = $frontendTypoScript->getSetupArray();
        } catch (\RuntimeException) {
            return '';
        }
        if (!is_array($setup)) {
            return '';
        }
        return (string) ($setup['plugin.']['tx_vhs.']['settings.']['prependPath'] ?? '');
    }

    protected function resolveFrontendController(): ?object
    {
        return static::resolveFrontendControllerStatic($this->resolveRequest());
    }

    protected static function resolveFrontendControllerStatic(?ServerRequestInterface $request): ?object
    {
        if (!$request instanceof ServerRequestInterface) {
            return null;
        }
        $frontendController = $request->getAttribute('frontend.controller');
        return is_object($frontendController) ? $frontendController : null;
    }

    protected function readSiteUrlFromRequest(ServerRequestInterface $request): string
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
}
