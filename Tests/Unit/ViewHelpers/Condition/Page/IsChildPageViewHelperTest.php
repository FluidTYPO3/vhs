<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\DatabaseConnection;

/**
 * Class IsChildPageViewHelperTest
 */
class IsChildPageViewHelperTest extends AbstractViewHelperTest
{

    public function testRender()
    {
        if (class_exists(\TYPO3\CMS\Core\Database\ConnectionPool::class)) {
            $this->markTestSkipped('Test is skippped on TYPO3v8 for now, due to tested code having tight coupling to Doctrine');
        }
        $GLOBALS['TYPO3_DB'] = $this->getMockBuilder(DatabaseConnection::class)->setMethods(['exec_SELECTquery'])->disableOriginalConstructor()->getMock();
        $GLOBALS['TYPO3_DB']->expects($this->any())->method('exec_SELECTquery')->will($this->returnValue(false));
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'pageUid' => 0
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }
}
