<?php

namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use FluidTYPO3\Vhs\Utility\VersionUtility;
use FluidTYPO3\Vhs\ViewHelpers\Resource\AbstractImageViewHelper;
use TYPO3\CMS\Core\Imaging\ImageResource;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class AbstractImageViewHelperTest extends AbstractTestCase
{
    private ?AbstractImageViewHelper $subject = null;
    private ?ContentObjectRenderer $contentObjectRenderer = null;

    protected function setUp(): void
    {
        $this->subject = $this->getMockBuilder(AbstractImageViewHelper::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->contentObjectRenderer = $this->getMockBuilder(ContentObjectRenderer::class)
            ->setMethods(['getImgResource'])
            ->disableOriginalConstructor()
            ->getMock();

        parent::setUp();

        $this->simulateRequestWithExtbaseParameters('', 123, $this->contentObjectRenderer);
    }

    public function testProcessImage(): void
    {
        $this->runTestWithImage(false);
    }

    public function testProcessImageWithOnlyProperties(): void
    {
        $this->runTestWithImage(true);
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

    private function runTestWithImage(bool $onlyProperties): void
    {
        $path = '/path/to/file';

        $file = $this->getMockBuilder(File::class)
            ->setMethods(
                [
                    'getStorage',
                    'hasProperty',
                    'getProperty',
                    'getProperties',
                    'toArray',
                    'getName',
                    'getIdentifier',
                    'getPublicUrl',
                ]
            )
            ->disableOriginalConstructor()
            ->getMock();

        $infoArray = [
            123,
            456,
            'jpg',
            $path,
            $path,
            'origFile' => $file,
            'origFile_mtime' => 0,
        ];

        $storage = $this->getMockBuilder(ResourceStorage::class)
            ->setMethods(['getFileInfo'])
            ->disableOriginalConstructor()
            ->getMock();
        $storage->method('getFileInfo')->willReturn($infoArray);

        $file->method('getStorage')->willReturn($storage);
        $file->method('hasProperty')->willReturn(true);
        $file->method('getProperty')->willReturn('prop');
        $file->method('getName')->willReturn($path);
        $file->method('getPublicUrl')->willReturn($path);
        $file->method('getIdentifier')->willReturn('name');
        $file->method('getProperties')->willReturn($infoArray);
        $file->method('toArray')->willReturn($infoArray);

        if (VersionUtility::isCoreAtLeast13()) {
            $resource = new ImageResource(123, 456, 'jpg', $path, $path, $file);
        } else {
            $resource = $infoArray;
        }

        if ($onlyProperties) {
            $expectedFile = $infoArray;
        } else {
            $expectedFile = $file;
        }

        $this->contentObjectRenderer->method('getImgResource')->willReturn($resource);

        self::assertSame(
            [
                [
                    'info' => $infoArray,
                    'source' => $path,
                    'file' => $expectedFile,
                ],
            ],
            $this->subject->preprocessImages([$file], $onlyProperties)
        );
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
        $tsfe = $this->getMockBuilder(TypoScriptFrontendController::class)
            ->disableOriginalConstructor()
            ->getMock();
        $tsfe->tmpl = (object) [
            'setup' => ['plugin.' => ['tx_vhs.' => ['settings.' => ['prependPath' => 'prepend']]]],
        ];
        $this->simulateRequestWithExtbaseParameters('', 123, $this->contentObjectRenderer, $tsfe);

        $output = $this->subject->preprocessSourceUri('source');
        self::assertSame('prependsource', $output);
    }

    public function testPreProcessSourceUriInBackendContext(): void
    {
        $this->subject->setArguments(['relative' => false]);
        $output = $this->subject->preprocessSourceUri('source');
        self::assertStringEndsWith('source', $output);
    }
}
