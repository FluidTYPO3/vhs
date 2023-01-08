<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LocalizationFactory;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class LViewHelperTest
 */
class LViewHelperTest extends AbstractViewHelperTestCase
{
    protected function setUp(): void
    {
        $this->singletonInstances[ConfigurationManagerInterface::class] = $this->getMockBuilder(ConfigurationManagerInterface::class)->getMockForAbstractClass();
        $this->singletonInstances[LocalizationFactory::class] = $this->getMockBuilder(LocalizationFactory::class)
            ->setMethods(['getParsedData'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->singletonInstances[LocalizationFactory::class]->method('getParsedData')->willReturn([]);
        if (class_exists(ObjectManager::class)) {
            $this->singletonInstances[ObjectManager::class] = $this->getMockBuilder(ObjectManager::class)
                ->setMethods(['get'])
                ->disableOriginalConstructor()
                ->getMock();
            $this->singletonInstances[ObjectManager::class]->method('get')->willReturn(
                $this->getMockBuilder(ConfigurationManagerInterface::class)->getMockForAbstractClass()
            );
        }

        parent::setUp();

        $GLOBALS['TSFE'] = $this->getMockBuilder(TypoScriptFrontendController::class)->disableOriginalConstructor()->getMock();
        $GLOBALS['LANG'] = $this->getMockBuilder(LanguageService::class)->disableOriginalConstructor()->getMock();
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
