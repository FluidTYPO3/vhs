<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

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
use TYPO3\CMS\Core\Resource\ResourceFactory;

/**
 * Class FilesViewHelperTest
 */
class FilesViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @var string
     */
    protected $fixturesPath;

    /**
     * Setup
     */
    public function setUp(): void
    {
        $this->singletonInstances[ResourceFactory::class] = $this->getMockBuilder(ResourceFactory::class)->disableOriginalConstructor()->getMock();
        parent::setUp();
        $this->fixturesPath = 'EXT:vhs/Tests/Fixtures/Files';
        $packageManager = $this->getMockBuilder(PackageManager::class)->setMethods(['resolvePackagePath'])->disableOriginalConstructor()->getMock();
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
    public function returnsEmtpyArrayWhenArgumentsAreNotSet()
    {
        $this->assertEquals([], $this->executeViewHelper());
    }

    /**
     * @test
     */
    public function returnsEmptyArrayWhenPathIsInaccessible()
    {
        $this->assertEquals([], $this->executeViewHelperUsingTagContent('/this/path/hopefully/does/not/exist'));
    }

    /**
     * @test
     */
    public function returnsPopulatedArrayOfAllFoundFiles()
    {
        $actualFiles = glob($this->fixturesPath . '/*');
        $actualFilesCount = count($actualFiles);
        $this->assertCount($actualFilesCount, $this->executeViewHelperUsingTagContent($this->fixturesPath));
    }

    /**
     * @test
     */
    public function returnsPopulatedArrayOfFilteredFiles()
    {
        $actualFiles = glob($this->fixturesPath . '/*.txt');
        $actualFilesCount = count($actualFiles);
        $this->assertCount($actualFilesCount, $this->executeViewHelperUsingTagContent($this->fixturesPath));
    }
}
