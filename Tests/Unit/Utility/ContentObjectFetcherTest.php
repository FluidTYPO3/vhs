<?php
namespace FluidTYPO3\Vhs\Tests\Unit\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use FluidTYPO3\Vhs\Utility\ContentObjectFetcher;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class ContentObjectFetcherTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function explicitRequestWinsOverGlobalAndConfigurationRequest(): void
    {
        $globalContentObject = $this->createContentObjectRenderer();
        $configurationContentObject = $this->createContentObjectRenderer();
        $activeContentObject = $this->createContentObjectRenderer();

        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute(
            'currentContentObject',
            $globalContentObject
        );

        $configurationManager = $this->getMockBuilder(ConfigurationManagerInterface::class)
            ->addMethods(['getRequest'])
            ->getMockForAbstractClass();
        $configurationManager->method('getRequest')->willReturn(
            (new ServerRequest())->withAttribute('currentContentObject', $configurationContentObject)
        );

        $activeRequest = (new ServerRequest())->withAttribute('currentContentObject', $activeContentObject);

        self::assertSame(
            $activeContentObject,
            ContentObjectFetcher::resolve($configurationManager, $activeRequest)
        );
    }

    private function createContentObjectRenderer(): ContentObjectRenderer
    {
        return $this->getMockBuilder(ContentObjectRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
