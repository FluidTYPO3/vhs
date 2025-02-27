<?php

namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Resource\AbstractImageViewHelper;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class AbstractImageViewHelperTest extends AbstractTestCase
{
    private ?AbstractImageViewHelper $subject = null;
    private ?ContentObjectRenderer $contentObjectRenderer = null;

    protected function setUp(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->getMockBuilder(ServerRequest::class)
            ->setMethods(['getAttribute'])
            ->disableOriginalConstructor()
            ->getMock();
        $GLOBALS['TYPO3_REQUEST']->method('getAttribute')->willReturn(SystemEnvironmentBuilder::REQUESTTYPE_FE);

        $this->subject = $this->getMockBuilder(AbstractImageViewHelper::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->contentObjectRenderer = $this->getMockBuilder(ContentObjectRenderer::class)
            ->setMethods(['getImgResource'])
            ->disableOriginalConstructor()
            ->getMock();

        if (method_exists(ConfigurationManagerInterface::class, 'getContentObject')) {
            /** @var ConfigurationManagerInterface $configurationManager */
            $configurationManager = $this->getMockBuilder(ConfigurationManagerInterface::class)->getMock();
            $configurationManager->method('getContentObject')->willReturn($this->contentObjectRenderer);
        } else {
            $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
            $request->method('getAttribute')->willReturn($this->contentObjectRenderer);
            /** @var ConfigurationManagerInterface $configurationManager */
            $configurationManager = $this->getMockBuilder(ConfigurationManagerInterface::class)
                ->onlyMethods(['getConfiguration', 'setConfiguration', 'setRequest'])
                ->addMethods(['getRequest'])
                ->getMock();
            $configurationManager->method('getRequest')->willReturn($request);
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
                        789,
                        $path,
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
            ->setMethods(['getFileInfo'])
            ->disableOriginalConstructor()
            ->getMock();
        $storage->method('getFileInfo')->willReturn(['foo' => 'bar']);

        $file = $this->getMockBuilder(File::class)
            ->setMethods(['getStorage', 'hasProperty', 'getProperty', 'getProperties', 'toArray'])
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
                        789,
                        '/path/to/file',
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
        $GLOBALS['TSFE'] = (object) ['lastImageInfo' => null, 'imagesOnPage' => [], 'absRefPrefix' => ''];
        $this->contentObjectRenderer->method('getImgResource')->willReturn(
            [
                123,
                456,
                789,
                $path
            ]
        );
        return $this->subject->preprocessImages([$file], $onlyProperties);
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
        $GLOBALS['TSFE'] = (object) [
            'tmpl' => (object) ['setup' => ['plugin.' => ['tx_vhs.' => ['settings.' => ['prependPath' => 'prepend']]]]],
        ];

        $output = $this->subject->preprocessSourceUri('source');
        self::assertSame('prependsource', $output);
    }

    public function testPreProcessSourceUriInBackendContext(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->getMockBuilder(ServerRequest::class)
            ->setMethods(['getAttribute'])
            ->disableOriginalConstructor()
            ->getMock();
        $GLOBALS['TYPO3_REQUEST']->method('getAttribute')->willReturn(SystemEnvironmentBuilder::REQUESTTYPE_BE);

        $output = $this->subject->preprocessSourceUri('source');
        self::assertSame(GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . 'source', $output);
    }
}
