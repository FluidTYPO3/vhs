<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format\Placeholder;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Classes\AccessibleExtensionManagementUtility;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class LipsumViewHelperTest
 */
class LipsumViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @var array
     */
    protected $arguments = [
        'paragraphs' => 5,
        'skew' => 0,
        'html' => false,
        'parseFuncTSPath' => ''
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $packageManager = $this->getMockBuilder(PackageManager::class)
            ->setMethods(['resolvePackagePath'])
            ->disableOriginalConstructor()
            ->getMock();
        $packageManager->method('resolvePackagePath')->willReturnMap(
            [
                ['EXT:vhs/Tests/Fixtures/Files/foo.txt', 'Tests/Fixtures/Files/foo.txt'],
            ]
        );
        AccessibleExtensionManagementUtility::setPackageManager($packageManager);

        $GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects'] = [];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        AccessibleExtensionManagementUtility::setPackageManager(null);
        unset($GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects']);
    }

    /**
     * @test
     */
    public function supportsParagraphCount()
    {
        $arguments = $this->arguments;
        $firstRender = $this->executeViewHelper($arguments);
        $arguments['paragraphs'] = 6;
        $secondRender = $this->executeViewHelper($arguments);
        $this->assertLessThan(strlen($secondRender), strlen($firstRender));
    }

    /**
     * @test
     */
    public function supportsHtmlArgument()
    {
        $mockContentObject = $this->getMockBuilder(ContentObjectRenderer::class)->setMethods(['parseFunc'])->getMock();
        $mockContentObject->expects($this->once())->method('parseFunc')->willReturn('foobar');
        $configurationManager = $this->getMockBuilder(ConfigurationManagerInterface::class)->getMockForAbstractClass();
        $configurationManager->method('getContentObject')->willReturn($mockContentObject);
        $objectManager = $this->getMockBuilder(ObjectManager::class)->setMethods(['get'])->disableOriginalConstructor()->getMock();
        $objectManager->method('get')->willReturn($configurationManager);

        $singletons = GeneralUtility::getSingletonInstances();
        GeneralUtility::setSingletonInstance(ObjectManager::class, $objectManager);

        $arguments = $this->arguments;
        $arguments['html'] = true;
        $test = $this->executeViewHelper($arguments);

        GeneralUtility::resetSingletonInstances($singletons);

        $this->assertNotEmpty($test);
    }

    /**
     * @test
     */
    public function detectsFileByShortPath()
    {
        $arguments = $this->arguments;
        $arguments['lipsum'] = 'EXT:vhs/Tests/Fixtures/Files/foo.txt';
        $test = $this->executeViewHelper($arguments);
        $this->assertNotEmpty($test);
    }

    /**
     * @test
     */
    public function canFallBackWhenUsingFileAndFileDoesNotExist()
    {
        $arguments = $this->arguments;
        $arguments['lipsum'] = 'None.txt';
        $test = $this->executeViewHelper($arguments);
        $this->assertNotEmpty($test);
    }
}
