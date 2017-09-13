<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Content\Resources;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Core\Database\DatabaseConnection;

/**
 * Class FalViewHelperTest
 */
class FalViewHelperTest extends AbstractViewHelperTest
{

    public function testRender()
    {
        if (!$this->usesLegacyFluidVersion()) {
            $this->markTestSkipped('Test skipped pending refactoring to Doctrine QueryBuilder');
        }
        $GLOBALS['TYPO3_DB'] = $this->getMockBuilder(DatabaseConnection::class)->setMethods(['fullQuoteStr', 'exec_SELECTquery', 'sql_fetch_assoc'])->disableOriginalConstructor()->getMock();
        $GLOBALS['TYPO3_DB']->expects($this->any())->method('fullQuoteStr')->willReturnArgument(0);
        $GLOBALS['TYPO3_DB']->expects($this->any())->method('exec_SELECTquery')->willReturn(null);
        $GLOBALS['TYPO3_DB']->expects($this->any())->method('sql_fetch_assoc')->willReturn([]);
        $this->assertEmpty($this->executeViewHelper());
    }
}
