<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class IsLanguageViewHelperTest
 */
class IsLanguageViewHelperTest extends AbstractViewHelperTestCase
{
    public function testRender()
    {
        $GLOBALS['TYPO3_DB'] = $this->getMockBuilder(DatabaseConnection::class)->setMethods(['exec_SELECTgetSingleRow'])->disableOriginalConstructor()->getMock();
        $GLOBALS['TYPO3_DB']->expects($this->any())->method('exec_SELECTgetSingleRow')->will($this->returnValue(false));

        $GLOBALS['TSFE'] = $this->getMockBuilder(TypoScriptFrontendController::class)->disableOriginalConstructor()->getMock();
        $GLOBALS['TSFE']->expects($this->any())->method('__get')->with('sys_language_uid')->willReturn(0);

        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'language' => 0
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);
    }
}
