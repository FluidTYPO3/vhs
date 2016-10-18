<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Class InfoViewHelperTest
 */
class InfoViewHelperTest extends AbstractViewHelperTest
{
    public function testReturnsCorrectSingleFieldValue()
    {
        $expectedFieldValue = 42;

        $pageRepository = $this->getMock(PageRepository::class, array('dummy'));
        $GLOBALS['TSFE'] = (object) array('sys_page' => $pageRepository);
        $GLOBALS['TYPO3_DB'] = $this->getMock(DatabaseConnection::class, array('exec_SELECTgetSingleRow'), array(), '', false);
        $GLOBALS['TYPO3_DB']->expects($this->any())->method('exec_SELECTgetSingleRow')->willReturn(['tx_foo_bar' => $expectedFieldValue]);
        $this->assertEquals($expectedFieldValue, $this->executeViewHelper(['pageUid' => 12, 'field' => 'tx_foo_bar']));
    }

    public function testReturnsPageRowIfNoFieldGiven()
    {
        $expectedRow = ['uid' => 42, 'tx_foo_bar' => 'baz'];

        $pageRepository = $this->getMock(PageRepository::class, array('dummy'));
        $GLOBALS['TSFE'] = (object) array('sys_page' => $pageRepository);
        $GLOBALS['TYPO3_DB'] = $this->getMock(DatabaseConnection::class, array('exec_SELECTgetSingleRow'), array(), '', false);
        $GLOBALS['TYPO3_DB']->expects($this->any())->method('exec_SELECTgetSingleRow')->willReturn($expectedRow);
        $this->assertEquals($expectedRow, $this->executeViewHelper(['pageUid' => 42]));
    }
}
