<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Media;

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
 * Class ExtensionViewHelperTest
 */
class ExtensionViewHelperTest extends AbstractViewHelperTestCase
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
        $this->singletonInstances[ResourceFactoryProxy::class] = $this->getMockBuilder(ResourceFactoryProxy::class)->disableOriginalConstructor()->getMock();
        parent::setUp();
        $this->fixturesPath = 'Tests/Fixtures/Files';
        $packageManager = $this->getMockBuilder(PackageManager::class)->setMethods(['resolvePackagePath'])->disableOriginalConstructor()->getMock();
        $packageManager->method('resolvePackagePath')->willReturnMap(
            [
                ['EXT:vhs/Tests/Fixtures/Files/foo.txt', 'Tests/Fixtures/Files/foo.txt'],
                ['EXT:vhs/Tests/Fixtures/Files/noext', 'Tests/Fixtures/Files/noext'],
                ['EXT:vhs/Tests/Fixtures/Files', 'Tests/Fixtures/Files'],
            ]
        );
        AccessibleExtensionManagementUtility::setPackageManager($packageManager);
    }

    /**
     * @test
     */
    public function returnsEmptyStringForEmptyArguments()
    {
        $this->assertEquals('', $this->executeViewHelper());
    }

    /**
     * @test
     */
    public function returnsExpectedExtensionForProvidedPath()
    {
        $this->assertEquals('txt', $this->executeViewHelperUsingTagContent($this->fixturesPath . '/foo.txt'));
    }

    /**
     * @test
     */
    public function returnsEmptyStringForFileWithoutExtension()
    {
        $this->assertEquals('', $this->executeViewHelperUsingTagContent($this->fixturesPath . '/noext'));
    }
}
