<?php
declare(strict_types=1);

namespace FluidTYPO3\Vhs\Middleware;

use FluidTYPO3\Vhs\Service\AssetService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\Stream;

class AssetInclusion implements MiddlewareInterface
{
    public function __construct(private readonly AssetService $assetService)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $body = $response->getBody();
        $body->rewind();
        $contents = $body->getContents();
        $contentsBefore = $contents;

        $this->assetService->buildAllUncached([], $request, $contents);

        if ($contentsBefore === $contents) {
            // Content is unchanged, return the original response since there is no need to modify it, or the
            // content-length header. Case triggers when rendered page contains no VHS assets.
            return $response;
        }

        /** @var resource $stream */
        $stream = fopen('php://temp', 'rw+');
        fputs($stream, $contents);

        return $response->withBody(new Stream($stream));
    }
}
