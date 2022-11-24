<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Extension\Path;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Classes\AccessibleExtensionManagementUtility;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Core\Package\Package;
use TYPO3\CMS\Core\Package\PackageManager;

/**
 * Class SiteRelativeViewHelperTest
 */
class SiteRelativeViewHelperTest extends AbstractViewHelperTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $package = $this->getMockBuilder(Package::class)->setMethods(['getPackagePath'])->disableOriginalConstructor()->getMock();
        $package->method('getPackagePath')->willReturn(realpath(__DIR__ . '/../../../../../'));

        $packageManager = $this->getMockBuilder(PackageManager::class)
            ->setMethods(['isPackageActive', 'getPackage'])
            ->disableOriginalConstructor()
            ->getMock();
        $packageManager->method('isPackageActive')->willReturnMap(
            [
                ['vhs', true],
                ['FakePlugin', false],
            ]
        );
        $packageManager->method('getPackage')->willReturn($package);
        AccessibleExtensionManagementUtility::setPackageManager($packageManager);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        AccessibleExtensionManagementUtility::setPackageManager(null);
    }

    /**
     * @test
     */
    public function rendersUsingArgument()
    {
        $test = $this->executeViewHelper(['extensionName' => 'Vhs']);
        $this->assertSame(realpath(__DIR__ . '/../../../../../'), $test);
    }

    /**
     * @test
     */
    public function rendersUsingControllerContext()
    {
        $this->controllerContext->getRequest()->setControllerExtensionName('Vhs');
        $test = $this->executeViewHelper([], [], null, 'Vhs');
        $this->assertSame(realpath(__DIR__ . '/../../../../../'), $test);
    }

    /**
     * @test
     */
    public function throwsErrorWhenUnableToDetectExtensionName()
    {
        $this->expectExceptionCode(1364167519);
        $this->executeViewHelper([], [], null, null, 'FakePlugin');
    }
}
