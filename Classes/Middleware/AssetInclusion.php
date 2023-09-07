<?php
declare(strict_types=1);

namespace FluidTYPO3\Vhs\Middleware;

use FluidTYPO3\Vhs\Service\AssetService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\Stream;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class AssetInclusion implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $body = $response->getBody();
        $body->rewind();
        $contents = $body->getContents();
        $contentsBefore = $contents;

        /** @var AssetService $assetService */
        $assetService = GeneralUtility::makeInstance(AssetService::class);
        $assetService->buildAllUncached([], $GLOBALS['TSFE'], $contents);

        if ($contentsBefore === $contents) {
            // Content is unchanged, return the original response since there is no need to modify it, or the
            // content-length header. Case triggers when rendered page contains no VHS assets.
            return $response;
        }

        /** @var resource $stream */
        $stream = fopen('php://temp', 'rw+');
        fputs($stream, $contents);

        $response = $response->withBody(new Stream($stream));

        // Copied from \TYPO3\CMS\Frontend\Middleware\ContentLengthResponseHeader to ensure proper content-length.
        if ($GLOBALS['TSFE'] instanceof TypoScriptFrontendController) {
            if ((!isset($GLOBALS['TSFE']->config['config']['enableContentLengthHeader'])
                    || $GLOBALS['TSFE']->config['config']['enableContentLengthHeader'])
                && !$GLOBALS['TSFE']->isBackendUserLoggedIn() && !($GLOBALS['TYPO3_CONF_VARS']['FE']['debug'] ?? false)
                && !($GLOBALS['TSFE']->config['config']['debug'] ?? false) && !$GLOBALS['TSFE']->doWorkspacePreview()
            ) {
                $response = $response->withHeader('Content-Length', (string)$response->getBody()->getSize());
            }
        }
        return $response;
    }
}
