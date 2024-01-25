<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class LViewHelperTest
 */
class LViewHelperTest extends AbstractViewHelperTestCase
{
    protected function setUp(): void
    {
        $languageService = $this->getMockBuilder(LanguageService::class)->disableOriginalConstructor()->getMock();

        $cache = $this->getMockBuilder(FrontendInterface::class)->getMockForAbstractClass();
        $cache->method('has')->willReturn(true);
        $cache->method('get')->willReturn($languageService);

        $this->singletonInstances[ConfigurationManagerInterface::class] = $this->getMockBuilder(ConfigurationManagerInterface::class)->getMockForAbstractClass();

        $this->singletonInstances[CacheManager::class] = $this->getMockBuilder(CacheManager::class)
            ->setMethods(['getCache'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->singletonInstances[CacheManager::class]->method('getCache')->willReturn($cache);

        if (class_exists(ObjectManager::class)) {
            $this->singletonInstances[ObjectManager::class] = $this->getMockBuilder(ObjectManager::class)
                ->setMethods(['get'])
                ->disableOriginalConstructor()
                ->getMock();
            $this->singletonInstances[ObjectManager::class]->method('get')->willReturn(
                $this->getMockBuilder(ConfigurationManagerInterface::class)->getMockForAbstractClass()
            );
        }

        $this->mockForLocalizationUtilityCalls([]);

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($GLOBALS['TSFE'], $GLOBALS['LANG']);
    }

    public function testRender()
    {
        $this->assertSame(
            'key',
            $this->executeViewHelperUsingTagContent('key', ['extensionName' => 'Vhs'])
        );
    }
}
