<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Content\Resources;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class FalViewHelperTest
 */
class FalViewHelperTest extends AbstractViewHelperTest
{

    public function testRender()
    {
        $GLOBALS['TYPO3_DB'] = $this->getMock(
            'TYPO3\\CMS\\Core\\Database\\DatabaseConnection',
            array('fullQuoteStr', 'exec_SELECTquery', 'sql_fetch_assoc'),
            array(),
            '',
            false
        );
        $GLOBALS['TYPO3_DB']->expects($this->any())->method('fullQuoteStr')->willReturnArgument(0);
        $GLOBALS['TYPO3_DB']->expects($this->any())->method('exec_SELECTquery')->willReturn(null);
        $GLOBALS['TYPO3_DB']->expects($this->any())->method('sql_fetch_assoc')->willReturn(array());
        $this->assertEmpty($this->executeViewHelper());
    }
}
