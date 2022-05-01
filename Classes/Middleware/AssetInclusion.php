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
use TYPO3\CMS\Extbase\Object\ObjectManager;

class AssetInclusion implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $body = $response->getBody();
        $body->rewind();
        $contents = $body->getContents();

        GeneralUtility::makeInstance(ObjectManager::class)->get(AssetService::class)->buildAll([], null, true, $contents);

        $stream = fopen('php://temp', 'rw+');
        fputs($stream, $contents);

        return $response->withBody(new Stream($stream));
    }
}
