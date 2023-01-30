<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Variable\Register;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class SetViewHelperTest extends AbstractViewHelperTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['TSFE'] = $this->getMockBuilder(TypoScriptFrontendController::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     */
    public function silentlyIgnoresMissingFrontendController()
    {
        $result = $this->executeViewHelper(['name' => 'name']);
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function canSetRegister()
    {
        $name = uniqid();
        $value = uniqid();
        $this->executeViewHelper(['name' => $name, 'value' => $value]);
        $this->assertEquals($value, $GLOBALS['TSFE']->register[$name]);
    }

    /**
     * @test
     */
    public function canSetVariableWithValueFromTagContent()
    {
        $name = uniqid();
        $value = uniqid();
        $this->executeViewHelperUsingTagContent($value, ['name' => $name]);
        $this->assertEquals($value, $GLOBALS['TSFE']->register[$name]);
    }
}
