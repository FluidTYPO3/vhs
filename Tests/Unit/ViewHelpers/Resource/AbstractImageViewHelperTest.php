<?php

namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Classes\RequestAwareConfigurationManager;
use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Resource\AbstractImageViewHelper;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Imaging\ImageResource;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class AbstractImageViewHelperTest extends AbstractTestCase
{
    private AbstractImageViewHelper $subject;
    /**
     * @var ContentObjectRenderer&MockObject
     */
    private ContentObjectRenderer $contentObjectRenderer;

    protected function setUp(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->getMockBuilder(ServerRequest::class)
            ->onlyMethods(['getAttribute'])
            ->disableOriginalConstructor()
            ->getMock();
        $GLOBALS['TYPO3_REQUEST']->method('getAttribute')->willReturnMap(
            [
                ['applicationType', null, SystemEnvironmentBuilder::REQUESTTYPE_FE],
            ]
        );

        $this->subject = $this->getMockBuilder(AbstractImageViewHelper::class)
            ->onlyMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->subject->setRenderingContext($this->createRenderingContextWithRequest($GLOBALS['TYPO3_REQUEST']));
        $this->contentObjectRenderer = $this->getMockBuilder(ContentObjectRenderer::class)
            ->onlyMethods(['getImgResource'])
            ->disableOriginalConstructor()
            ->getMock();

        if (method_exists(ConfigurationManagerInterface::class, 'getContentObject')) {
            /** @var ConfigurationManagerInterface&MockObject $configurationManager */
            $configurationManager = $this->createMock(ConfigurationManagerInterface::class);
            $configurationManager->method('getContentObject')->willReturn($this->contentObjectRenderer);
        } else {
            $request = $this->createMock(ServerRequestInterface::class);
            $request->method('getAttribute')->willReturn($this->contentObjectRenderer);
            $configurationManager = new RequestAwareConfigurationManager($request);
        }

        $this->subject->injectConfigurationManager($configurationManager);

        parent::setUp();
    }

    public function testProcessImage(): void
    {
        $path = 'https://foo.bar/path/to/file';
        $file = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();
        self::assertSame(
            [
                [
                    'info' => [
                        123,
                        456,
                        'jpg',
                        $path,
                        'origFile' => null,
                        'origFile_mtime' => null,
                    ],
                    'source' => $path,
                    'file' => $file,
                ],
            ],
            $this->runTestWithImage($file, $path, false),
        );
    }

    public function testProcessImageWithOnlyProperties(): void
    {
        $path = '/path/to/file';

        $storage = $this->getMockBuilder(ResourceStorage::class)
            ->onlyMethods(['getFileInfo'])
            ->disableOriginalConstructor()
            ->getMock();
        $storage->method('getFileInfo')->willReturn(['foo' => 'bar']);

        $file = $this->getMockBuilder(File::class)
            ->onlyMethods(['getStorage', 'hasProperty', 'getProperty', 'getProperties', 'toArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $file->method('getStorage')->willReturn($storage);
        $file->method('hasProperty')->willReturn(true);
        $file->method('getProperty')->willReturn('prop');
        $file->method('getProperties')->willReturn(['foo' => 'bar']);
        $file->method('toArray')->willReturn(['foo' => 'bar']);

        self::assertSame(
            [
                [
                    'info' => [
                        123,
                        456,
                        'jpg',
                        '/path/to/file',
                        'origFile' => null,
                        'origFile_mtime' => null,
                    ],
                    'source' => $path,
                    'file' => ['foo' => 'bar'],
                ],
            ],
            $this->runTestWithImage($file, $path, true),
        );
    }

    public function testProcessImageThrowsExceptionOnInvalidImage(): void
    {
        $files = [
            $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock(),
        ];
        $this->contentObjectRenderer->method('getImgResource')->willReturn(null);
        self::expectExceptionCode(1253191060);
        $this->subject->preprocessImages($files);
    }

    private function runTestWithImage(File $file, string $path, bool $onlyProperties): array
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->getMockBuilder(ServerRequest::class)
            ->onlyMethods(['getAttribute'])
            ->disableOriginalConstructor()
            ->getMock();
        $GLOBALS['TYPO3_REQUEST']->method('getAttribute')->willReturnMap(
            [
                ['applicationType', null, SystemEnvironmentBuilder::REQUESTTYPE_FE],
                ['frontend.controller', null, (object) ['lastImageInfo' => null, 'imagesOnPage' => []]],
            ]
        );
        $this->subject->setRenderingContext($this->createRenderingContextWithRequest($GLOBALS['TYPO3_REQUEST']));
        $this->contentObjectRenderer->method('getImgResource')->willReturn(
            new ImageResource(
                123,
                456,
                'jpg',
                $path,
            )
        );
        $result = $this->subject->preprocessImages([$file], $onlyProperties);
        self::assertIsArray($result);
        return $result;
    }

    public function testProcessImageDoesNotThrowExceptionWithInvalidImageIfGracefulEnabled(): void
    {
        $files = [
            $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock(),
        ];
        $this->contentObjectRenderer->method('getImgResource')->willReturn(null);
        $this->subject->setArguments(['graceful' => true]);
        $output = $this->subject->preprocessImages($files);
        self::assertSame([], $output);
    }

    public function testPreProcessSourceUriWithPrependPath(): void
    {
        $frontendTypoScript = new class () {
            public function hasSetup(): bool
            {
                return true;
            }

            public function getSetupArray(): array
            {
                return ['plugin.' => ['tx_vhs.' => ['settings.' => ['prependPath' => 'prepend']]]];
            }
        };

        $GLOBALS['TYPO3_REQUEST'] = $this->getMockBuilder(ServerRequest::class)
            ->onlyMethods(['getAttribute'])
            ->disableOriginalConstructor()
            ->getMock();
        $GLOBALS['TYPO3_REQUEST']->method('getAttribute')->willReturnMap(
            [
                ['applicationType', null, SystemEnvironmentBuilder::REQUESTTYPE_FE],
                ['frontend.typoscript', null, $frontendTypoScript],
            ]
        );
        $this->subject->setRenderingContext($this->createRenderingContextWithRequest($GLOBALS['TYPO3_REQUEST']));

        $output = $this->subject->preprocessSourceUri('source');
        self::assertSame('prependsource', $output);
    }

    public function testPreProcessSourceUriInBackendContext(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->getMockBuilder(ServerRequest::class)
            ->onlyMethods(['getAttribute'])
            ->disableOriginalConstructor()
            ->getMock();
        $normalizedParams = $this->getMockBuilder(NormalizedParams::class)
            ->disableOriginalConstructor()
            ->getMock();
        $normalizedParams->method('getSiteUrl')->willReturn('https://example.test/sub/');
        $GLOBALS['TYPO3_REQUEST']->method('getAttribute')->willReturnMap(
            [
                ['applicationType', null, SystemEnvironmentBuilder::REQUESTTYPE_BE],
                ['normalizedParams', null, $normalizedParams],
            ]
        );
        $this->subject->setRenderingContext($this->createRenderingContextWithRequest($GLOBALS['TYPO3_REQUEST']));

        $output = $this->subject->preprocessSourceUri('source');
        self::assertSame('https://example.test/sub/source', $output);
    }

    public function testPreProcessSourceUriUsesRenderingContextRequest(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->createRequestWithSiteUrl('https://outer.example/outer/');
        $this->subject->setRenderingContext(
            $this->createRenderingContextWithRequest(
                $this->createRequestWithSiteUrl('https://inner.example/inner/')
            )
        );

        $output = $this->subject->preprocessSourceUri('source');

        self::assertSame('https://inner.example/inner/source', $output);
    }

    private function createRequestWithSiteUrl(string $siteUrl): ServerRequest
    {
        $normalizedParams = $this->getMockBuilder(NormalizedParams::class)
            ->disableOriginalConstructor()
            ->getMock();
        $normalizedParams->method('getSiteUrl')->willReturn($siteUrl);
        $request = $this->getMockBuilder(ServerRequest::class)
            ->onlyMethods(['getAttribute'])
            ->disableOriginalConstructor()
            ->getMock();
        $request->method('getAttribute')->willReturnMap(
            [
                ['applicationType', null, SystemEnvironmentBuilder::REQUESTTYPE_BE],
                ['normalizedParams', null, $normalizedParams],
            ]
        );
        return $request;
    }

    private function createRenderingContextWithRequest(ServerRequestInterface $request): RenderingContextInterface
    {
        if (!method_exists(RenderingContext::class, 'getRequest')) {
            return new class ($request) extends RenderingContext {
                private ServerRequestInterface $requestOverride;

                public function __construct(ServerRequestInterface $request)
                {
                    $this->requestOverride = $request;
                }

                public function getRequest(): ServerRequestInterface
                {
                    return $this->requestOverride;
                }
            };
        }

        $renderingContext = $this->getMockBuilder(RenderingContext::class)
            ->onlyMethods(['getRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $renderingContext->method('getRequest')->willReturn($request);

        return $renderingContext;
    }
}
