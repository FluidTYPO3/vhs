<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Once;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class CookieViewHelperTest
 */
class CookieViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @return void
     */
    public function testAssertShouldSkip()
    {
        $mock = $this->getMock($this->getViewHelperClassName(), array('getIdentifier'));
        $mock->expects($this->exactly(2))->method('getIdentifier')->willReturn('test');
        $this->assertFalse($this->callInaccessibleMethod($mock, 'assertShouldSkip'));
        $_COOKIE['test'] = 'test';
        $this->assertTrue($this->callInaccessibleMethod($mock, 'assertShouldSkip'));
        unset($_COOKIE['test']);
    }

    /**
     * @return void
     */
    public function testRemoveIfExpired()
    {
        $mock = $this->getMock($this->getViewHelperClassName(), array('getIdentifier', 'removeCookie'));
        $mock->expects($this->exactly(2))->method('getIdentifier')->willReturn('test');
        $mock->expects($this->once())->method('removeCookie');
        $this->callInaccessibleMethod($mock, 'removeIfExpired');
        $_COOKIE['test'] = 'test';
        $this->callInaccessibleMethod($mock, 'removeIfExpired');
        unset($_COOKIE['test']);
    }
}
