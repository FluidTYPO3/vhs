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
 * Class OrViewHelperTest
 */
class OrViewHelperTest extends AbstractViewHelperTestCase
{
    protected function setUp(): void
    {
        $this->singletonInstances[LocalizationFactory::class] = $this->getMockBuilder(LocalizationFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->singletonInstances[LocalizationFactory::class]->method('getParsedData')->willReturn([]);
        $this->singletonInstances[ConfigurationManagerInterface::class] = $this->getMockBuilder(ConfigurationManagerInterface::class)->getMockForAbstractClass();
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
        $GLOBALS['LANG'] = $this->getMockBuilder(LanguageService::class)
            ->setMethods(['dummy'])
            ->disableOriginalConstructor()
            ->getMock();
        $GLOBALS['TYPO3_REQUEST'] = null;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($GLOBALS['TSFE'], $GLOBALS['LANG'], $GLOBALS['TYPO3_REQUEST']);
    }

    /**
     * @test
     * @dataProvider getRenderTestValues
     * @param array $arguments
     * @param mixed $expected
     */
    public function testRender($arguments, $expected)
    {
        $result = $this->executeViewHelper($arguments);
        $content = $arguments['content'];
        unset($arguments['content']);
        $result2 = $this->executeViewHelperUsingTagContent((string) $content, $arguments);
        $this->assertEquals($expected, $result);
        $this->assertEquals($result, $result2);
    }

    /**
     * @return array
     */
    public function getRenderTestValues()
    {
        return [
            [['extensionName' => 'Vhs', 'content' => 'alt', 'alternative' => 'alternative'], 'alt'],
            [['extensionName' => 'Vhs', 'content' => '', 'alternative' => 'alternative'], 'alternative'],
            [['extensionName' => 'Vhs', 'content' => null, 'alternative' => 'alternative'], 'alternative'],
            [['extensionName' => 'Vhs', 'content' => 0, 'alternative' => 'alternative'], 'alternative'],
            [
                ['extensionName' => 'Vhs', 'content' => 0, 'alternative' => 'LLL:notfound'],
                'LLL:notfound'
            ],
        ];
    }
}
