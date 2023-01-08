<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Once;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Once\SessionViewHelper;

/**
 * Class SessionViewHelperTest
 */
class SessionViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @return void
     */
    public function testStoreIdentifier()
    {
        $instance = $this->createInstance();
        $this->callInaccessibleMethod($instance, 'storeIdentifier', ['identifier' => 'test']);
        $this->assertEquals(time(), $_SESSION[SessionViewHelper::class]['test']);
        unset($_SESSION[SessionViewHelper::class]['test']);
    }

    /**
     * @return void
     */
    public function testAssertShouldSkip()
    {
        $instance = $this->createInstance();
        $this->assertFalse($this->callInaccessibleMethod($instance, 'assertShouldSkip', ['identifier' => 'test']));
        $_SESSION[SessionViewHelper::class]['test'] = time();
        $this->assertTrue($this->callInaccessibleMethod($instance, 'assertShouldSkip', ['identifier' => 'test']));
        unset($_SESSION[SessionViewHelper::class]['test']);
    }

    /**
     * @return void
     */
    public function testRemoveIfExpired()
    {
        $instance = $this->createInstance();
        $time = time() - 10;
        $_SESSION[SessionViewHelper::class]['test'] = $time;

        $this->callInaccessibleMethod($instance, 'removeIfExpired', ['identifier' => 'test', 'ttl' => 15]);
        $this->assertArrayHasKey('test', $_SESSION[SessionViewHelper::class]);

        $this->callInaccessibleMethod($instance, 'removeIfExpired', ['identifier' => 'test', 'ttl' => 5]);
        $this->assertArrayNotHasKey('test', $_SESSION[SessionViewHelper::class]);
    }
}
