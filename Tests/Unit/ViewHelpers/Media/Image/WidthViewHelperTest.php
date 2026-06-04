<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Media\Image;

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
 * Class WidthViewHelperTest
 */
class WidthViewHelperTest extends AbstractViewHelperTestCase
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
        $this->singletonInstances[ResourceFactoryProxy::class] = $this->getMockBuilder(ResourceFactoryProxy::class)
            ->disableOriginalConstructor()
            ->getMock();
        parent::setUp();
        $fixturesPath = realpath(__DIR__ . '/../../../../../Tests/Fixtures/Files');
        if (!is_string($fixturesPath)) {
            throw new \RuntimeException('Unable to resolve fixture path.', 1780000417);
        }
        $this->fixturesPath = $fixturesPath;
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
    public function returnsZeroForEmptyArguments(): void
    {
        $this->assertSame(0, $this->executeViewHelper());
    }

    /**
     * @test
     */
    public function returnsFileWidthAsInteger(): void
    {
        $this->assertSame(385, $this->executeViewHelperUsingTagContent($this->fixturesPath . '/typo3_logo.jpg'));
    }

    /**
     * @test
     */
    public function throwsExceptionWhenFileNotFound(): void
    {
        $this->expectViewHelperException();
        $this->executeViewHelperUsingTagContent('/this/path/hopefully/does/not/exist.txt');
    }

    /**
     * @test
     */
    public function throwsExceptionWhenFileIsNotAccessibleOrIsADirectory(): void
    {
        $this->expectViewHelperException();
        $this->executeViewHelperUsingTagContent($this->fixturesPath);
    }
}
