<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format\Placeholder;
use PHPUnit\Framework\Attributes\Test;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Classes\AccessibleExtensionManagementUtility;
use FluidTYPO3\Vhs\Tests\Fixtures\Classes\RequestAwareConfigurationManager;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use PHPUnit\Framework\MockObject\MockObject;

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
            ->onlyMethods(['resolvePackagePath'])
            ->disableOriginalConstructor()
            ->getMock();
        $packageManager->method('resolvePackagePath')->willReturnMap(
            [
                ['EXT:vhs/Tests/Fixtures/Files/foo.txt', 'Tests/Fixtures/Files/foo.txt'],
            ]
        );
        AccessibleExtensionManagementUtility::setPackageManager($packageManager);

        $mockContentObject = $this->getMockBuilder(ContentObjectRenderer::class)
            ->onlyMethods(['parseFunc'])
            ->disableOriginalConstructor()
            ->getMock();
        $mockContentObject->method('parseFunc')->willReturn('foobar');

        if (method_exists(ConfigurationManagerInterface::class, 'getContentObject')) {
            /** @var ConfigurationManagerInterface&MockObject $configurationManager */
            $configurationManager = $this->createMock(ConfigurationManagerInterface::class);
            $configurationManager->method('getContentObject')->willReturn($mockContentObject);
        } else {
            $request = $this->createMock(ServerRequestInterface::class);
            $request->method('getAttribute')->willReturn($mockContentObject);
            $configurationManager = new RequestAwareConfigurationManager($request);
        }

        $this->singletonInstances[ConfigurationManagerInterface::class] = $configurationManager;

        $GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects'] = [];

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects']);
    }

    /**
     */
            #[Test]
    public function supportsParagraphCount(): void
    {
        $arguments = $this->arguments;
        $firstRender = $this->executeViewHelper($arguments);
        $arguments['paragraphs'] = 6;
        $secondRender = $this->executeViewHelper($arguments);
        self::assertIsString($firstRender);
        self::assertIsString($secondRender);
        $this->assertLessThan(strlen($secondRender), strlen($firstRender));
    }

    #[Test]
    public function supportsHtmlArgument(): void
    {
        $arguments = $this->arguments;
        $arguments['html'] = true;
        $test = $this->executeViewHelper($arguments);

        $this->assertNotEmpty($test);
    }

    #[Test]
    public function detectsFileByShortPath(): void
    {
        $arguments = $this->arguments;
        $arguments['lipsum'] = 'EXT:vhs/Tests/Fixtures/Files/foo.txt';
        $test = $this->executeViewHelper($arguments);
        $this->assertNotEmpty($test);
    }

    #[Test]
    public function canFallBackWhenUsingFileAndFileDoesNotExist(): void
    {
        $arguments = $this->arguments;
        $arguments['lipsum'] = 'None.txt';
        $test = $this->executeViewHelper($arguments);
        $this->assertNotEmpty($test);
    }
}
