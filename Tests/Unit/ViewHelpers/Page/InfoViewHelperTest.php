<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Class InfoViewHelperTest
 */
class InfoViewHelperTest extends AbstractViewHelperTest
{
    public function testReturnsCorrectSingleFieldValue()
    {
        if (class_exists(\TYPO3\CMS\Core\Database\ConnectionPool::class)) {
            $this->markTestSkipped('Test is skippped on TYPO3v8 for now, due to tested code having tight coupling to Doctrine');
        }
        $expectedFieldValue = 42;

        $this->mockPageRepository();
        $GLOBALS['TSFE']->sys_page->expects($this->any())->method('getPage_noCheck')->willReturn(['tx_foo_bar' => $expectedFieldValue]);
        $this->assertEquals($expectedFieldValue, $this->executeViewHelper(['pageUid' => 12, 'field' => 'tx_foo_bar']));
    }

    public function testReturnsPageRowIfNoFieldGiven()
    {
        if (class_exists(\TYPO3\CMS\Core\Database\ConnectionPool::class)) {
            $this->markTestSkipped('Test is skippped on TYPO3v8 for now, due to tested code having tight coupling to Doctrine');
        }
        $expectedRow = ['uid' => 42, 'tx_foo_bar' => 'baz'];

        $this->mockPageRepository();
        $GLOBALS['TSFE']->sys_page->expects($this->any())->method('getPage_noCheck')->willReturn($expectedRow);
        $this->assertEquals($expectedRow, $this->executeViewHelper(['pageUid' => 42]));
    }

    public function testThrowsExceptionWhenUsedInBackend()
    {
        if (class_exists(\TYPO3\CMS\Core\Database\ConnectionPool::class)) {
            $this->markTestSkipped('Test is skippped on TYPO3v8 for now, due to tested code having tight coupling to Doctrine');
        }
        unset($GLOBALS['TSFE']);
        $this->expectViewHelperException();
        $this->executeViewHelper(['pageUid' => 42]);
    }

    private function mockPageRepository()
    {
        $pageRepository = $this->getMockBuilder(PageRepository::class)->setMethods(['getPage_noCheck'])->getMock();
        $GLOBALS['TSFE'] = (object) ['sys_page' => $pageRepository];
    }
}
