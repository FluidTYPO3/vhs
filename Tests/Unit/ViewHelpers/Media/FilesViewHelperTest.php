<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Proxy\ResourceFactoryProxy;
use FluidTYPO3\Vhs\Tests\Fixtures\Classes\AccessibleExtensionManagementUtility;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Core\Package\PackageManager;

/**
 * Class FilesViewHelperTest
 */
class FilesViewHelperTest extends AbstractViewHelperTestCase
{
    protected string $fixturesPath;

    /**
     * Setup
     */
    public function setUp(): void
    {
        $this->singletonInstances[ResourceFactoryProxy::class] = $this->getMockBuilder(ResourceFactoryProxy::class)
            ->disableOriginalConstructor()
            ->getMock();
        parent::setUp();
        $this->fixturesPath = 'EXT:vhs/Tests/Fixtures/Files';
        $packageManager = $this->getMockBuilder(PackageManager::class)
            ->onlyMethods(['resolvePackagePath'])
            ->disableOriginalConstructor()
            ->getMock();
        $packageManager->method('resolvePackagePath')->willReturnMap(
            [
                ['EXT:vhs/Tests/Fixtures/Files/typo3_logo.jpg', 'Tests/Fixtures/Files/typo3_logo.jpg'],
                ['EXT:vhs/Tests/Fixtures/Files', 'Tests/Fixtures/Files'],
            ]
        );
        AccessibleExtensionManagementUtility::setPackageManager($packageManager);
    }

    /**
     * @test
     */
    public function returnsEmtpyArrayWhenArgumentsAreNotSet(): void
    {
        $this->assertEquals([], $this->executeViewHelper());
    }

    /**
     * @test
     */
    public function returnsEmptyArrayWhenPathIsInaccessible(): void
    {
        $this->assertEquals([], $this->executeViewHelperUsingTagContent('/this/path/hopefully/does/not/exist'));
    }

    /**
     * @test
     */
    public function returnsPopulatedArrayOfAllFoundFiles(): void
    {
        $actualFiles = glob($this->fixturesPath . '/*');
        self::assertIsArray($actualFiles);
        $result = $this->executeViewHelperUsingTagContent($this->fixturesPath);
        self::assertIsArray($result);
        $actualFilesCount = count($actualFiles);
        $this->assertCount($actualFilesCount, $result);
    }

    /**
     * @test
     */
    public function returnsPopulatedArrayOfFilteredFiles(): void
    {
        $actualFiles = glob($this->fixturesPath . '/*.txt');
        self::assertIsArray($actualFiles);
        $result = $this->executeViewHelperUsingTagContent($this->fixturesPath);
        self::assertIsArray($result);
        $actualFilesCount = count($actualFiles);
        $this->assertCount($actualFilesCount, $result);
    }
}
