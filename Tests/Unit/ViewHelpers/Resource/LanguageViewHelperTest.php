<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Classes\AccessibleExtensionManagementUtility;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Core\Localization\LocalizationFactory;
use TYPO3\CMS\Core\Package\Package;
use TYPO3\CMS\Core\Package\PackageManager;

/**
 * Class LanguageViewHelperTest
 */
class LanguageViewHelperTest extends AbstractViewHelperTestCase
{
    protected function setUp(): void
    {
        $package = $this->getMockBuilder(Package::class)
            ->setMethods(['getPackagePath'])
            ->disableOriginalConstructor()
            ->getMock();
        $packageManager = $this->getMockBuilder(PackageManager::class)
            ->setMethods(['getPackage', 'isPackageActive'])
            ->disableOriginalConstructor()
            ->getMock();
        $packageManager->method('getPackage')->willReturn($package);
        $packageManager->method('isPackageActive')->willReturn(true);
        AccessibleExtensionManagementUtility::setPackageManager($packageManager);

        $this->singletonInstances[LocalizationFactory::class] = $this->getMockBuilder(LocalizationFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        parent::setUp();
    }

    /**
     * @test
     */
    public function testRenderFailsWhenUnableToResolveExtensionName()
    {
        $output = $this->executeViewHelper();
        self::assertSame([], $output);
    }
}
