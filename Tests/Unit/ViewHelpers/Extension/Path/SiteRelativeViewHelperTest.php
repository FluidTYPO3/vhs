<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Extension\Path;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Class SiteRelativeViewHelperTest
 */
class SiteRelativeViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function rendersUsingArgument()
    {
        $test = $this->executeViewHelper(['extensionName' => 'Vhs']);
        $this->assertSame(ExtensionManagementUtility::siteRelPath('vhs'), $test);
    }

    /**
     * @test
     */
    public function rendersUsingControllerContext()
    {
        $test = $this->executeViewHelper([], [], null, 'Vhs');
        $this->assertSame(ExtensionManagementUtility::siteRelPath('vhs'), $test);
    }

    /**
     * @test
     */
    public function throwsErrorWhenUnableToDetectExtensionName()
    {
        $this->setExpectedException('RuntimeException', null, 1364167519);
        $this->executeViewHelper([], [], null, null, 'FakePlugin');
    }
}
