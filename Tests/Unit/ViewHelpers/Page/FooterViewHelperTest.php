<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

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
use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * Class FooterViewHelperTest
 */
class FooterViewHelperTest extends AbstractViewHelperTestCase
{
    protected function setUp(): void
    {
        $this->singletonInstances[PageRenderer::class] = $this->getMockBuilder(PageRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();

        parent::setUp();

        $packageManager = $this->getMockBuilder(PackageManager::class)
            ->setMethods(['resolvePackagePath'])
            ->disableOriginalConstructor()
            ->getMock();
        $packageManager->method('resolvePackagePath')->willReturnMap(
            [
                ['EXT:core/Resources/Public/JavaScript/JavaScriptHandler.js', ''],
            ]
        );
        AccessibleExtensionManagementUtility::setPackageManager($packageManager);
    }

    public function testRender()
    {
        $result = $this->executeViewHelper();
        $this->assertEmpty($result);
    }
}
