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
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
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

        parent::setUp();

        $GLOBALS['TSFE'] = $this->getMockBuilder(TypoScriptFrontendController::class)->disableOriginalConstructor()->getMock();
        $GLOBALS['LANG'] = $this->getMockBuilder(LanguageService::class)->disableOriginalConstructor()->getMock();
        $GLOBALS['TYPO3_REQUEST'] = null;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($GLOBALS['TSFE'], $GLOBALS['LANG'], $GLOBALS['TYPO3_REQUEST']);
    }

    public function testRender()
    {
        $this->assertSame(
            'key',
            $this->executeViewHelperUsingTagContent('key', ['extensionName' => 'Vhs'])
        );
    }

    protected function createObjectManagerInstance(): ObjectManagerInterface
    {
        $instance = parent::createObjectManagerInstance();
        $instance->method('get')->willReturnMap(
            [
                [ConfigurationManagerInterface::class, $this->getMockBuilder(ConfigurationManagerInterface::class)->getMockForAbstractClass()],
            ]
        );
        return $instance;
    }
}
