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
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
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

        $mockContentObject = $this->getMockBuilder(ContentObjectRenderer::class)
            ->setMethods(['parseFunc'])
            ->disableOriginalConstructor()
            ->getMock();
        $mockContentObject->method('parseFunc')->willReturn('foobar');
        $this->singletonInstances[ConfigurationManager::class] = $this->getMockBuilder(ConfigurationManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->singletonInstances[ConfigurationManager::class]->method('getContentObject')
            ->willReturn($mockContentObject);

        $GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects'] = [];

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
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
        $arguments = $this->arguments;
        $arguments['html'] = true;
        $test = $this->executeViewHelper($arguments);

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
