<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Variable\Register;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class SetViewHelperTest
 */
class SetViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function silentlyIgnoresMissingFrontendController()
    {
        $result = $this->executeViewHelper(array('name' => 'name'));
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function canSetRegister()
    {
        $GLOBALS['TSFE'] = $this->getMock('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', array(), array(array(), 1, 1));
        $name = uniqid();
        $value = uniqid();
        $this->executeViewHelper(array('name' => $name, 'value' => $value));
        $this->assertEquals($value, $GLOBALS['TSFE']->register[$name]);
    }

    /**
     * @test
     */
    public function canSetVariableWithValueFromTagContent()
    {
        $GLOBALS['TSFE'] = $this->getMock('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', array(), array(array(), 1, 1));
        $name = uniqid();
        $value = uniqid();
        $this->executeViewHelperUsingTagContent('Text', $value, array('name' => $name));
        $this->assertEquals($value, $GLOBALS['TSFE']->register[$name]);
    }
}
