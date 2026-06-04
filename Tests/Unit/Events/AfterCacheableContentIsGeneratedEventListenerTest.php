<?php
namespace FluidTYPO3\Vhs\Tests\Unit\Events;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Events\AfterCacheableContentIsGeneratedEventListener;
use FluidTYPO3\Vhs\Service\AssetService;
use FluidTYPO3\Vhs\Tests\Fixtures\Classes\DummyTypoScriptFrontendController;
use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent;

class AfterCacheableContentIsGeneratedEventListenerTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function skipsAssetInjectionWhenAssetHandlingIsDisabled(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['vhs']['disableAssetHandling'] = true;

        $assetService = $this->getMockBuilder(AssetService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['buildAll'])
            ->getMock();
        $assetService->expects($this->never())->method('buildAll');

        $event = $this->createAfterCacheableContentIsGeneratedEvent('content');
        (new AfterCacheableContentIsGeneratedEventListener($assetService))->insertVhsAssetHeaderAndFooterCode($event);

        $this->assertEventContent('content', $event);
    }

    /**
     * @test
     */
    public function skipsAssetInjectionWhenLegacyAssetHandlingFlagIsDisabled(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['vhs']['setup']['disableAssetHandling'] = '1';

        $assetService = $this->getMockBuilder(AssetService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['buildAll'])
            ->getMock();
        $assetService->expects($this->never())->method('buildAll');

        $event = $this->createAfterCacheableContentIsGeneratedEvent('content');
        (new AfterCacheableContentIsGeneratedEventListener($assetService))->insertVhsAssetHeaderAndFooterCode($event);

        $this->assertEventContent('content', $event);
    }

    private function createAfterCacheableContentIsGeneratedEvent(string $content): AfterCacheableContentIsGeneratedEvent
    {
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '14.0', '<')) {
            return new AfterCacheableContentIsGeneratedEvent(
                new ServerRequest(),
                new DummyTypoScriptFrontendController(),
                'cache-identifier',
                true
            );
        }

        return new AfterCacheableContentIsGeneratedEvent(new ServerRequest(), $content, 'cache-identifier', true);
    }

    private function assertEventContent(string $expected, AfterCacheableContentIsGeneratedEvent $event): void
    {
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '14.0', '<')
            || !method_exists($event, 'getContent')
        ) {
            self::assertTrue(true);
            return;
        }

        self::assertSame($expected, $event->getContent());
    }
}
