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
 * Class SessionViewHelperTest
 */
class SessionViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @return void
     */
    public function testStoreIdentifier()
    {
        $instance = $this->createInstance();
        $instance->setArguments(array('identifier' => 'test'));
        $this->callInaccessibleMethod($instance, 'storeIdentifier');
        $this->assertEquals(time(), $_SESSION[get_class($instance)]['test']);
        unset($_SESSION[get_class($instance)]['test']);
    }

    /**
     * @return void
     */
    public function testAssertShouldSkip()
    {
        $instance = $this->createInstance();
        $instance->setArguments(array('identifier' => 'test'));
        $this->assertFalse($this->callInaccessibleMethod($instance, 'assertShouldSkip'));
        $_SESSION[get_class($instance)]['test'] = time();
        $this->assertTrue($this->callInaccessibleMethod($instance, 'assertShouldSkip'));
        unset($_SESSION[get_class($instance)]['test']);
    }

    /**
     * @return void
     */
    public function testRemoveIfExpired()
    {
        $instance = $this->createInstance();
        $class = $this->getViewHelperClassName();
        $time = time() - 10;
        $_SESSION[$class]['test'] = $time;

        $instance->setArguments(array('identifier' => 'test', 'ttl' => 15));
        $this->callInaccessibleMethod($instance, 'removeIfExpired');
        $this->assertArrayHasKey('test', $_SESSION[$class]);

        $instance->setArguments(array('identifier' => 'test', 'ttl' => 5));
        $this->callInaccessibleMethod($instance, 'removeIfExpired');
        $this->assertArrayNotHasKey('test', $_SESSION[$class]);
    }
}
