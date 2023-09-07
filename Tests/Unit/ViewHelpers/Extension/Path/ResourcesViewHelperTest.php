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
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * Class ResourcesViewHelperTest
 */
class ResourcesViewHelperTest extends AbstractViewHelperTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $package = $this->getMockBuilder(Package::class)->setMethods(['getPackagePath'])->disableOriginalConstructor()->getMock();
        $package->method('getPackagePath')->willReturn('');

        $packageManager = $this->getMockBuilder(PackageManager::class)
            ->setMethods(['resolvePackagePath', 'isPackageActive', 'getPackage'])
            ->disableOriginalConstructor()
            ->getMock();
        $packageManager->method('resolvePackagePath')->willReturnMap(
            [
                ['vhs', ''],
            ]
        );
        $packageManager->method('isPackageActive')->willReturnMap(
            [
                ['vhs', true],
                ['FakePlugin', false],
            ]
        );
        $packageManager->method('getPackage')->willReturn($package);
        AccessibleExtensionManagementUtility::setPackageManager($packageManager);
    }

    /**
     * @test
     */
    public function rendersUsingArgument()
    {
        $test = $this->executeViewHelper(['extensionName' => 'Vhs', 'path' => 'ext_icon.gif']);
        $extPath = ExtensionManagementUtility::extPath('vhs', 'Resources/Public/ext_icon.gif');
        $extPath = PathUtility::stripPathSitePrefix($extPath);
        $this->assertSame($extPath, $test);
    }
}
