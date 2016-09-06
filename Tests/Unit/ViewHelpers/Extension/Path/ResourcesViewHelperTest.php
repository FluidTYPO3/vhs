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
 * Class ResourcesViewHelperTest
 */
class ResourcesViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function rendersUsingArgument()
    {
        $test = $this->executeViewHelper(array('extensionName' => 'Vhs', 'path' => 'ext_icon.gif'));
        $this->assertSame(ExtensionManagementUtility::extRelPath('vhs') . 'Resources/Public/ext_icon.gif', $test);
    }

    /**
     * @test
     */
    public function rendersUsingControllerContext()
    {
        $test = $this->executeViewHelper(array('path' => 'ext_icon.gif'), array(), null, 'Vhs');
        $this->assertSame(ExtensionManagementUtility::extRelPath('vhs') . 'Resources/Public/ext_icon.gif', $test);
    }

    /**
     * @test
     */
    public function throwsErrorWhenUnableToDetectExtensionName()
    {
        $this->setExpectedException('RuntimeException', null, 1364167519);
        $this->executeViewHelper(array(), array(), null, null, 'FakePlugin');
    }
}
