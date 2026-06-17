<?php
namespace FluidTYPO3\Vhs\Tests\Unit\Middleware;

use FluidTYPO3\Vhs\Middleware\AssetInclusion;
use FluidTYPO3\Vhs\Service\AssetService;
use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\ServerRequest;

class AssetInclusionTest extends AbstractTestCase
{
    #[Test]
    public function skipsNonHtmlResponses(): void
    {
        $response = (new Response())
            ->withHeader('Content-Type', 'image/png');
        $response->getBody()->write('png-binary');

        $assetService = $this->createMock(AssetService::class);
        $assetService
            ->expects(self::never())
            ->method('buildAllUncached');

        $middleware = new AssetInclusion($assetService);
        $result = $middleware->process(
            new ServerRequest(),
            new class($response) implements RequestHandlerInterface {
                public function __construct(private readonly ResponseInterface $response)
                {
                }

                public function handle(ServerRequestInterface $request): ResponseInterface
                {
                    return $this->response;
                }
            }
        );

        self::assertSame($response, $result);
    }
}
