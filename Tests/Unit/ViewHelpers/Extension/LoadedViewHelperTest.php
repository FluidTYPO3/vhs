<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Extension;

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

/**
 * Class LoadedViewHelperTest
 */
class LoadedViewHelperTest extends AbstractViewHelperTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $packageManager = $this->getMockBuilder(PackageManager::class)
            ->setMethods(['isPackageActive'])
            ->disableOriginalConstructor()
            ->getMock();
        $packageManager->method('isPackageActive')->willReturnMap(
            [
                ['vhs', true],
                ['void', false],
            ]
        );
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
    public function rendersThenChildIfExtensionIsLoaded()
    {
        $arguments = [
            'extensionName' => 'Vhs',
            'then' => 1,
            'else' => 0,
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertSame(1, $result);
    }

    /**
     * @test
     */
    public function rendersElseChildIfExtensionIsNotLoaded()
    {
        $arguments = [
            'extensionName' => 'Void',
             'then' => 1,
             'else' => 0,
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertSame(0, $result);
    }
}
