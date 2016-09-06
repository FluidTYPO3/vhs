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
 * Class RootlineViewHelperTest
 */
class RootlineViewHelperTest extends AbstractViewHelperTest
{

    public function testRender()
    {
        $pageRepository = $this->getMock(PageRepository::class, array('dummy'));
        $GLOBALS['TSFE'] = (object) array('sys_page' => $pageRepository);
        $GLOBALS['TYPO3_DB'] = $this->getMock('TYPO3\\CMS\\Core\\Database\\DatabaseConnection', array('exec_SELECTgetSingleRow'), array(), '', false);
        $GLOBALS['TYPO3_DB']->expects($this->any())->method('exec_SELECTgetSingleRow')->willReturn(false);
        $this->assertEmpty($this->executeViewHelper());
    }
}
