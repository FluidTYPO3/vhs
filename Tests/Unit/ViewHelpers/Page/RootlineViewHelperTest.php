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
 * Class RootlineViewHelperTest
 */
class RootlineViewHelperTest extends AbstractViewHelperTest
{

    public function testRender()
    {
        if (class_exists(\TYPO3\CMS\Core\Database\ConnectionPool::class)) {
            $this->markTestSkipped('Test is skippped on TYPO3v8 for now, due to tested code having tight coupling to Doctrine');
        }
        $pageRepository = $this->getMockBuilder(PageRepository::class)->setMethods(['dummy'])->getMock();
        $GLOBALS['TSFE'] = (object) ['sys_page' => $pageRepository];
        $GLOBALS['TYPO3_DB'] = $this->getMockBuilder(DatabaseConnection::class)->setMethods(['exec_SELECTgetSingleRow'])->disableOriginalConstructor()->getMock();
        $GLOBALS['TYPO3_DB']->expects($this->any())->method('exec_SELECTgetSingleRow')->willReturn(false);
        $this->assertEmpty($this->executeViewHelper());
    }
}
